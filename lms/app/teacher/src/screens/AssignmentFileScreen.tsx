import React, { useEffect, useMemo, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';
import { WebView } from 'react-native-webview';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import PinchZoomImage from '../components/PinchZoomImage';
import { LmsApi } from '../api/lms';
import { API_BASE_URL, APP_ROLE, PRIMARY, PUBLIC_BASE_URL } from '../config';
import { AppStorage } from '../storage/AppStorage';
import {
  assignmentAuthFileUrl,
  assignmentPublicFileUrl,
  buildPdfViewerHtml,
  fetchAuthenticatedBase64,
  fetchImageDataUri,
  getAssignmentFileMode,
  type AssignmentFileMode,
} from '../utils/assignmentFileView';
import { WorkStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<WorkStackParamList, 'AssignmentFile'>;

export default function AssignmentFileScreen({ navigation, route }: Props) {
  const { id, index = 0 } = route.params;
  const [token, setToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [fileMode, setFileMode] = useState<AssignmentFileMode | null>(null);
  const [fileName, setFileName] = useState('File');
  const [pdfHtml, setPdfHtml] = useState<string | null>(null);
  const [imageUri, setImageUri] = useState<string | null>(null);
  const [usePublicFallback, setUsePublicFallback] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const authUri = assignmentAuthFileUrl(API_BASE_URL, APP_ROLE, id, index);
  const publicUri = assignmentPublicFileUrl(PUBLIC_BASE_URL, id, index);
  const activeUri = usePublicFallback ? publicUri : authUri;

  useEffect(() => {
    setUsePublicFallback(false);
    setFileMode(null);
    setImageUri(null);
    setError(null);
    setLoading(true);
  }, [id, index]);

  useEffect(() => {
    let active = true;
    (async () => {
      const [authToken, assignmentRes] = await Promise.all([
        AppStorage.getToken(),
        LmsApi.assignment(id).catch(() => null),
      ]);
      if (!active) return;
      setToken(authToken);
      const assignment = (assignmentRes as { assignment?: { files?: { index: number; name: string }[]; document?: string; file_name?: string } } | null)?.assignment;
      const files = assignment?.files ?? [];
      const selected = files.find(file => file.index === index) ?? files[index];
      const displayName = selected?.name ?? assignment?.file_name ?? assignment?.document ?? '';
      setFileName(displayName || 'File');
      setFileMode(getAssignmentFileMode(displayName));
    })();
    return () => {
      active = false;
    };
  }, [id, index]);

  useEffect(() => {
    if (!token || fileMode !== 'pdf') return;
    let active = true;
    setLoading(true);
    setError(null);
    setPdfHtml(null);
    fetchAuthenticatedBase64(authUri, token)
      .then(base64 => {
        if (!active) return;
        setPdfHtml(buildPdfViewerHtml(base64));
      })
      .catch(() => {
        if (!active) return;
        setError('Could not load PDF.');
      })
      .finally(() => {
        if (active) setLoading(false);
      });
    return () => {
      active = false;
    };
  }, [token, fileMode, authUri]);

  useEffect(() => {
    if (fileMode !== 'image') return;
    let active = true;
    setLoading(true);
    setError(null);
    setImageUri(null);
    fetchImageDataUri(authUri, token, publicUri)
      .then(dataUri => {
        if (!active) return;
        setImageUri(dataUri);
      })
      .catch(() => {
        if (!active) return;
        setError('Could not load image.');
      })
      .finally(() => {
        if (active) setLoading(false);
      });
    return () => {
      active = false;
    };
  }, [fileMode, authUri, token, publicUri]);

  const webViewSource = useMemo(() => {
    if (fileMode === 'pdf' && pdfHtml) {
      return { html: pdfHtml };
    }
    if (fileMode === 'document' && token) {
      if (usePublicFallback) {
        return { uri: activeUri };
      }
      return { uri: activeUri, headers: { Authorization: `Bearer ${token}` } };
    }
    return null;
  }, [activeUri, fileMode, pdfHtml, token, usePublicFallback]);

  const showLoader = !token || fileMode === null || loading;

  return (
    <ScreenLayout title={fileName} subtitle="In-app viewer" onBack={() => navigation.goBack()} scroll={false}>
      <View style={styles.viewer}>
        {imageUri ? (
          <View style={styles.imageWrap}>
            <PinchZoomImage uri={imageUri} />
          </View>
        ) : null}
        {webViewSource ? (
          <WebView
            key={activeUri}
            source={webViewSource}
            style={styles.media}
            originWhitelist={['*']}
            javaScriptEnabled
            domStorageEnabled
            allowsInlineMediaPlayback
            onLoadStart={() => setLoading(true)}
            onLoadEnd={() => setLoading(false)}
            onError={() => {
              if (!usePublicFallback) {
                setUsePublicFallback(true);
                setError(null);
                setLoading(true);
                return;
              }
              setLoading(false);
              setError('Could not load file.');
            }}
            onHttpError={() => {
              if (!usePublicFallback) {
                setUsePublicFallback(true);
                setError(null);
                setLoading(true);
                return;
              }
              setLoading(false);
              setError('Could not load file.');
            }}
          />
        ) : null}
        {error && !showLoader ? (
          <View style={styles.errorWrap}>
            <Text style={styles.errorText}>{error}</Text>
          </View>
        ) : null}
        {showLoader ? (
          <View style={styles.loadingOverlay}>
            <ActivityIndicator color={PRIMARY} size="large" />
            <Text style={styles.loadingText}>Loading file…</Text>
          </View>
        ) : null}
      </View>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  viewer: {
    flex: 1,
    backgroundColor: '#f1f5f9',
  },
  imageWrap: {
    flex: 1,
    backgroundColor: '#f1f5f9',
  },
  media: {
    flex: 1,
    width: '100%',
    backgroundColor: '#f1f5f9',
  },
  loadingOverlay: {
    ...StyleSheet.absoluteFillObject,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f1f5f9',
  },
  loadingText: { marginTop: 12, color: '#64748b' },
  errorWrap: {
    ...StyleSheet.absoluteFillObject,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
  },
  errorText: { color: '#64748b', textAlign: 'center' },
});
