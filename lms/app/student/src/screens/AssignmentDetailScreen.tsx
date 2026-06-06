import React, { useCallback, useState } from 'react';
import {
  Text,
  Pressable,
  StyleSheet,
  Alert,
  Share,
  ActivityIndicator,
  Linking,
  View,
} from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import {
  AssignmentDetailRow,
  AssignmentHeroCard,
} from '../components/AssignmentUi';
import { LmsApi } from '../api/lms';
import { useStudentContext } from '../hooks/useStudentContext';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import AppIcon from '../components/AppIcon';
import { AssignmentsStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<AssignmentsStackParamList, 'AssignmentDetail'>;

function normalizeUrl(value?: string | null) {
  const text = String(value ?? '').trim();
  if (!text) return null;
  if (/^https?:\/\//i.test(text)) return text;
  return `https://${text}`;
}

export default function AssignmentDetailScreen({ navigation, route }: Props) {
  const { id } = route.params;
  const ctx = useStudentContext();
  const [loading, setLoading] = useState(true);
  const [item, setItem] = useState<any>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.assignment(id);
      setItem(res.assignment);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not load assignment');
    } finally {
      setLoading(false);
    }
  }, [id]);

  React.useEffect(() => {
    load();
  }, [load]);

  const isLink = String(item?.type ?? '').toLowerCase() === 'link';
  const linkUrl = normalizeUrl(item?.link_url ?? item?.document);

  async function openLink() {
    if (!linkUrl) {
      Alert.alert('No link', 'This assignment does not have a valid link.');
      return;
    }
    try {
      await Linking.openURL(linkUrl);
    } catch {
      Alert.alert('Could not open link', linkUrl);
    }
  }

  return (
    <ScreenLayout
      title="Assignment"
      subtitle={item?.document_name ?? 'Details'}
      onBack={() => navigation.goBack()}
      refreshing={loading}
      onRefresh={load}>
      {loading && !item ? <ActivityIndicator color={PRIMARY} style={styles.loader} /> : null}
      {item ? (
        <>
          <AssignmentHeroCard
            title={item.document_name}
            type={item.type}
            batch={item.batch_name ?? item.batch}
          />

          <Card title="Details">
            <AssignmentDetailRow label="Course" value={ctx.course} />
            <AssignmentDetailRow label="Batch" value={item.batch_name ?? item.batch} />
            <AssignmentDetailRow label="Type" value={isLink ? 'Link' : 'File'} />
            {isLink ? (
              <AssignmentDetailRow label="Link" value={item.link_url ?? item.document} />
            ) : (
              <AssignmentDetailRow label="File" value={item.document} />
            )}
          </Card>

          {isLink ? (
            <View style={styles.actions}>
              <Pressable style={styles.primaryBtn} onPress={openLink}>
                <AppIcon name="open-outline" family="ionicons" size={18} color="#fff" />
                <Text style={styles.primaryBtnText}>Open link</Text>
              </Pressable>
              <Pressable
                style={styles.secondaryBtn}
                onPress={() =>
                  Share.share({
                    message: linkUrl ?? String(item.link_url ?? ''),
                    title: item.document_name,
                  })
                }>
                <AppIcon name="share-outline" family="ionicons" size={18} color={PRIMARY} />
                <Text style={styles.secondaryBtnText}>Share link</Text>
              </Pressable>
            </View>
          ) : (
            <Pressable
              style={styles.primaryBtn}
              onPress={() => navigation.navigate('AssignmentFile', { id })}>
              <AppIcon name="document-text-outline" family="ionicons" size={18} color="#fff" />
              <Text style={styles.primaryBtnText}>View file in app</Text>
            </Pressable>
          )}
        </>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  loader: { marginTop: 24 },
  actions: { gap: 10 },
  primaryBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: PRIMARY,
    paddingVertical: 15,
    borderRadius: 14,
    marginBottom: 10,
    shadowColor: PRIMARY,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.25,
    shadowRadius: 10,
    elevation: 3,
  },
  primaryBtnText: { color: '#fff', fontWeight: '800', fontSize: 15 },
  secondaryBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: theme.card,
    paddingVertical: 15,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: theme.border,
    marginBottom: 14,
  },
  secondaryBtnText: { color: PRIMARY, fontWeight: '800', fontSize: 15 },
});
