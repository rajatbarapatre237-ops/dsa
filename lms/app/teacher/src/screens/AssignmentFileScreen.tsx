import React, { useEffect, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';
import { WebView } from 'react-native-webview';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { API_BASE_URL, APP_ROLE, PRIMARY } from '../config';
import { AppStorage } from '../storage/AppStorage';
import { WorkStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<WorkStackParamList, 'AssignmentFile'>;

export default function AssignmentFileScreen({ navigation, route }: Props) {
  const { id } = route.params;
  const [token, setToken] = useState<string | null>(null);
  const uri = `${API_BASE_URL}/${APP_ROLE}/assignments/${id}/file`;

  useEffect(() => {
    AppStorage.getToken().then(setToken);
  }, []);

  return (
    <ScreenLayout title="Assignment file" subtitle="In-app viewer" onBack={() => navigation.goBack()} scroll={false}>
      <View style={styles.viewer}>
        {!token ? (
          <ActivityIndicator color={PRIMARY} />
        ) : (
          <WebView
            source={{ uri, headers: { Authorization: `Bearer ${token}` } }}
            style={styles.web}
            startInLoadingState
            renderLoading={() => (
              <View style={styles.loading}>
                <ActivityIndicator color={PRIMARY} size="large" />
                <Text style={styles.loadingText}>Loading file…</Text>
              </View>
            )}
          />
        )}
      </View>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  viewer: { flex: 1, minHeight: 480, borderRadius: 8, overflow: 'hidden', backgroundColor: '#f1f5f9' },
  web: { flex: 1 },
  loading: { flex: 1, justifyContent: 'center', alignItems: 'center', padding: 24 },
  loadingText: { marginTop: 12, color: '#64748b' },
});
