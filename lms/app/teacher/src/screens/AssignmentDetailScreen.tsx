import React, { useCallback, useState } from 'react';
import {
  Text,
  StyleSheet,
  Pressable,
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
  AssignmentHeroCard,
  AssignmentInfoRow,
  AssignmentStatusToggle,
  AssignmentFilesList,
} from '../components/AssignmentUi';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { platformWeight } from '../ui/typography';
import { WorkStackParamList } from '../navigation/types';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';

type Props = NativeStackScreenProps<WorkStackParamList, 'AssignmentDetail'>;

function normalizeUrl(value?: string | null) {
  const text = String(value ?? '').trim();
  if (!text) return null;
  if (/^https?:\/\//i.test(text)) return text;
  return `https://${text}`;
}

function isActiveStatus(status: unknown) {
  return status === 1 || status === '1' || status === true;
}

export default function AssignmentDetailScreen({ navigation, route }: Props) {
  const { id } = route.params;
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [item, setItem] = useState<any>(null);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.assignment(id);
        setItem(res.assignment);
        markHasData();
      } catch (e: any) {
        Alert.alert('Error', e?.message ?? 'Could not load assignment');
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, id, markHasData],
  );

  useRefreshOnFocus(load);

  const isLink = String(item?.type ?? '').toLowerCase() === 'link';
  const isNote = item?.content_kind === 'note';
  const linkUrl = normalizeUrl(item?.link_url ?? item?.document);
  const active = isActiveStatus(item?.status);
  const editRoute = isNote ? 'EditNote' : 'EditAssignment';
  const files = (item?.files ?? []) as { index: number; name: string }[];
  const fileCount = Number(item?.file_count ?? files.length);

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

  async function toggleStatus(on: boolean) {
    try {
      await LmsApi.updateAssignmentStatus(id, on);
      setItem((prev: any) => (prev ? { ...prev, status: on ? 1 : 0 } : prev));
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not update status');
    }
  }

  function confirmDelete() {
    Alert.alert('Delete assignment', `Remove "${item?.document_name}"?`, [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Delete',
        style: 'destructive',
        onPress: async () => {
          try {
            await LmsApi.deleteAssignment(id);
            navigation.goBack();
          } catch (e: any) {
            Alert.alert('Error', e?.message ?? 'Could not delete');
          }
        },
      },
    ]);
  }

  return (
    <ScreenLayout
      title={isNote ? 'Note' : 'Assignment'}
      subtitle={item?.document_name ?? 'Details'}
      onBack={() => navigation.goBack()}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      {loading && !item ? <ActivityIndicator color={PRIMARY} style={styles.loader} /> : null}
      {item ? (
        <>
          <AssignmentHeroCard
            title={item.document_name}
            type={item.type}
            batch={item.batch_name}
            active={active}
          />

          <AssignmentStatusToggle active={active} onChange={toggleStatus} />

          <Card title="Details">
            <AssignmentInfoRow icon="document-text-outline" label="Title" value={item.document_name} />
            <AssignmentInfoRow icon="book-outline" label="Subject" value={item.subject_name} />
            <AssignmentInfoRow icon="people-outline" label="Batch" value={item.batch_name} />
            <AssignmentInfoRow
              icon={isLink ? 'link-outline' : 'document-attach-outline'}
              label="Type"
              value={isLink ? 'Link' : fileCount > 1 ? `${fileCount} files` : 'File'}
            />
            {isLink ? (
              <AssignmentInfoRow icon="globe-outline" label="Link" value={item.link_url ?? item.document} last />
            ) : fileCount <= 1 ? (
              <AssignmentInfoRow icon="folder-outline" label="File" value={item.file_name ?? item.document} last />
            ) : null}
          </Card>

          {!isLink && files.length > 0 ? (
            <AssignmentFilesList
              files={files}
              onOpen={index => navigation.navigate('AssignmentFile', { id, index })}
            />
          ) : null}

          {isLink ? (
            <View style={styles.actions}>
              <Pressable style={styles.primaryBtn} onPress={openLink}>
                <AppIcon name="open-outline" family="ionicons" size={18} color="#fff" />
                <Text style={[styles.primaryBtnText, platformWeight('800')]}>Open link</Text>
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
                <Text style={[styles.secondaryBtnText, platformWeight('800')]}>Share link</Text>
              </Pressable>
            </View>
          ) : fileCount === 1 ? (
            <Pressable
              style={styles.primaryBtn}
              onPress={() => navigation.navigate('AssignmentFile', { id, index: files[0]?.index ?? 0 })}>
              <AppIcon name="document-text-outline" family="ionicons" size={18} color="#fff" />
              <Text style={[styles.primaryBtnText, platformWeight('800')]}>View file in app</Text>
            </Pressable>
          ) : null}

          <Pressable
            style={styles.secondaryBtn}
            onPress={() => navigation.navigate(editRoute, { id })}>
            <AppIcon name="create-outline" family="ionicons" size={18} color={PRIMARY} />
            <Text style={[styles.secondaryBtnText, platformWeight('800')]}>Edit</Text>
          </Pressable>

          <Pressable style={styles.dangerBtn} onPress={confirmDelete}>
            <AppIcon name="trash-outline" family="ionicons" size={18} color={theme.danger} />
            <Text style={[styles.dangerBtnText, platformWeight('700')]}>Delete assignment</Text>
          </Pressable>
        </>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  loader: { marginTop: 24 },
  actions: { gap: 10, marginBottom: 10 },
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
  primaryBtnText: { color: '#fff', fontSize: 15 },
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
  secondaryBtnText: { color: PRIMARY, fontSize: 15 },
  dangerBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#fff5f5',
    paddingVertical: 14,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: '#fecaca',
    marginBottom: 20,
  },
  dangerBtnText: { color: theme.danger, fontSize: 15 },
});
