import React, { useEffect, useMemo, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View, Image, ScrollView } from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import { FormInput } from '../components/FormInput';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { useThemeColors } from '../ui/useThemeColors';
import { WorkStackParamList } from '../navigation/types';
import {
  pickImagesFromGallery,
  takePhoto,
  type PickedFile,
} from '../utils/mediaPicker';

type AssignType = 'link' | 'file';
type ContentKind = 'assignment' | 'note';

export default function AddAssignmentScreen() {
  const navigation = useNavigation();
  const route = useRoute<RouteProp<WorkStackParamList, 'AddAssignment' | 'AddNote'>>();
  const colors = useThemeColors();
  const contentKind: ContentKind =
    route.name === 'AddNote' || route.params?.contentKind === 'note' ? 'note' : 'assignment';
  const isNote = contentKind === 'note';

  const [batches, setBatches] = useState<{ label: string; value: string }[]>([]);
  const [subjects, setSubjects] = useState<{ label: string; value: string }[]>([]);
  const [type, setType] = useState<AssignType>(isNote ? 'file' : 'link');
  const [batch, setBatch] = useState('');
  const [subjectId, setSubjectId] = useState('');
  const [documentName, setDocumentName] = useState('');
  const [link, setLink] = useState('');
  const [files, setFiles] = useState<PickedFile[]>([]);
  const [saving, setSaving] = useState(false);

  const screenTitle = useMemo(() => (isNote ? 'Add Note' : 'Add Assignment'), [isNote]);

  useEffect(() => {
    LmsApi.allBatches()
      .then((res: any) => {
        setBatches(
          (res.batches ?? []).map((b: any) => ({
            label: `${b.name} (${b.course})`,
            value: b.name,
          })),
        );
      })
      .catch(() => {});
  }, []);

  useEffect(() => {
    if (!batch) {
      setSubjects([]);
      setSubjectId('');
      return;
    }
    LmsApi.subjectsForBatch(batch)
      .then((res: any) => {
        const options = (res.subjects ?? []).map((s: any) => ({
          label: s.subject_name,
          value: String(s.id),
        }));
        setSubjects(options);
        if (!options.length) {
          Alert.alert('No subjects', 'No subjects found for this batch. Ask admin to assign subjects to the course.');
        }
      })
      .catch((e: any) => {
        setSubjects([]);
        Alert.alert('Subjects', e?.message ?? 'Could not load subjects for this batch');
      });
  }, [batch]);

  function addFiles(picked: PickedFile[]) {
    if (!picked.length) return;
    setFiles(prev => [...prev, ...picked]);
  }

  function removeFile(key: string) {
    setFiles(prev => prev.filter(file => file.key !== key));
  }

  function clearFiles() {
    setFiles([]);
  }

  async function pickFile() {
    try {
      const { pick, types } = await import('@react-native-documents/picker');
      const results = await pick({
        type: [types.pdf, types.plainText, types.images, types.allFiles],
        allowMultiSelection: true,
      });
      const picked = results
        .filter(r => r.uri)
        .map((r, index) => ({
          uri: r.uri!,
          name: r.name ?? (isNote ? `note-${index + 1}` : `assignment-${index + 1}`),
          type: r.type ?? 'application/octet-stream',
          key: `${r.uri}-${r.name}-${Date.now()}-${index}`,
        }));
      addFiles(picked);
    } catch (e: any) {
      const mod = await import('@react-native-documents/picker');
      if (mod.isErrorWithCode(e) && e.code === mod.errorCodes.OPERATION_CANCELED) return;
      Alert.alert('File picker', e?.message ?? 'Could not pick file');
    }
  }

  async function pickFromGallery() {
    addFiles(await pickImagesFromGallery());
  }

  async function capturePhoto() {
    addFiles(await takePhoto());
  }

  async function uploadFile(
    file: PickedFile,
    title: string,
    subject: { label: string; value: string } | undefined,
  ) {
    const form = new FormData();
    form.append('type', 'file');
    form.append('content_kind', contentKind);
    form.append('batch_name', batch);
    form.append('document_name', title);
    form.append('subject_id', String(subjectId));
    if (subject?.label) form.append('subject_name', subject.label);
    form.append('file', {
      uri: file.uri,
      name: file.name,
      type: file.type,
    } as any);
    await LmsApi.createAssignmentFile(form);
  }

  async function save() {
    if (!batch || !documentName.trim()) {
      Alert.alert('Required', 'Select batch and enter a title');
      return;
    }
    if (!subjectId) {
      Alert.alert('Required', 'Select a subject');
      return;
    }
    setSaving(true);
    try {
      const subject = subjects.find(s => s.value === subjectId);
      const subjectPayload = {
        subject_id: Number(subjectId),
        subject_name: subject?.label,
        content_kind: contentKind,
      };

      if (type === 'link') {
        if (!link.trim()) {
          Alert.alert('Required', 'Enter link URL');
          return;
        }
        await LmsApi.createAssignmentLink({
          type: 'link',
          batch_name: batch,
          document_name: documentName.trim(),
          link: link.trim(),
          ...subjectPayload,
        });
      } else {
        if (!files.length) {
          Alert.alert('Required', 'Add at least one photo or file to upload');
          return;
        }
        const baseTitle = documentName.trim();
        for (let i = 0; i < files.length; i++) {
          const title = files.length > 1 ? `${baseTitle} (${i + 1})` : baseTitle;
          await uploadFile(files[i], title, subject);
        }
      }
      Alert.alert('Success', isNote ? 'Note added' : 'Assignment added', [
        { text: 'OK', onPress: () => navigation.navigate('WorkHub' as never) },
      ]);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not save');
    } finally {
      setSaving(false);
    }
  }

  return (
    <ScreenLayout
      title={screenTitle}
      subtitle={isNote ? 'Upload class notes by subject' : 'Share homework by subject'}
      onBack={() => navigation.goBack()}>
      <Card>
        {!isNote ? (
          <FormPicker
            label="Type"
            value={type}
            options={[
              { label: 'Link', value: 'link' },
              { label: 'File / photo', value: 'file' },
            ]}
            onChange={v => setType(v as AssignType)}
          />
        ) : null}
        <FormPicker label="Batch" value={batch} options={batches} onChange={setBatch} />
        <FormPicker label="Subject" value={subjectId} options={subjects} onChange={setSubjectId} />
        <FormInput
          label={isNote ? 'Note title' : 'Document name'}
          value={documentName}
          onChangeText={setDocumentName}
          placeholder={isNote ? 'e.g. Chapter 3 summary' : 'e.g. DBMS Chapter 1'}
        />
        {type === 'link' ? (
          <FormInput
            label="Link URL"
            value={link}
            onChangeText={setLink}
            placeholder="https://..."
            autoCapitalize="none"
          />
        ) : (
          <View style={styles.uploadSection}>
            <Text style={[styles.uploadLabel, { color: colors.muted }]}>Upload photo or file</Text>
            <View style={styles.uploadRow}>
              <Pressable
                style={[styles.uploadTile, { backgroundColor: colors.primarySoft, borderColor: colors.border }]}
                onPress={capturePhoto}>
                <AppIcon name="camera-outline" size={26} color={PRIMARY} />
                <Text style={styles.uploadTileText}>Camera</Text>
              </Pressable>
              <Pressable
                style={[styles.uploadTile, { backgroundColor: colors.primarySoft, borderColor: colors.border }]}
                onPress={pickFromGallery}>
                <AppIcon name="image-outline" size={26} color={PRIMARY} />
                <Text style={styles.uploadTileText}>Gallery</Text>
              </Pressable>
              <Pressable
                style={[styles.uploadTile, { backgroundColor: colors.primarySoft, borderColor: colors.border }]}
                onPress={pickFile}>
                <AppIcon name="document-attach-outline" family="ionicons" size={26} color={PRIMARY} />
                <Text style={styles.uploadTileText}>File</Text>
              </Pressable>
            </View>

            {files.length ? (
              <View style={styles.filesHeader}>
                <Text style={[styles.filesCount, { color: colors.text }]}>
                  {files.length} file{files.length === 1 ? '' : 's'} selected
                </Text>
                <Pressable onPress={clearFiles} hitSlop={8}>
                  <Text style={styles.clearAll}>Clear all</Text>
                </Pressable>
              </View>
            ) : null}

            {files.length ? (
              <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.previewRow}>
                {files.map(file => {
                  const isImage = file.type.startsWith('image/');
                  return (
                    <View
                      key={file.key}
                      style={[styles.previewCard, { backgroundColor: colors.surface, borderColor: colors.border }]}>
                      <Pressable style={styles.removeBtn} onPress={() => removeFile(file.key)} hitSlop={8}>
                        <AppIcon name="close-circle" size={22} color="#dc2626" />
                      </Pressable>
                      {isImage ? (
                        <Image source={{ uri: file.uri }} style={styles.previewImage} resizeMode="cover" />
                      ) : (
                        <View style={styles.fileIconWrap}>
                          <AppIcon name="document-text-outline" size={32} color={PRIMARY} />
                        </View>
                      )}
                      <Text style={[styles.fileName, { color: colors.muted }]} numberOfLines={2}>
                        {file.name}
                      </Text>
                    </View>
                  );
                })}
              </ScrollView>
            ) : (
              <Text style={[styles.uploadHint, { color: colors.muted }]}>
                Tap Camera, Gallery, or File. You can add multiple items.
              </Text>
            )}
          </View>
        )}
        <Pressable style={[styles.saveBtn, saving && styles.disabled]} onPress={save} disabled={saving}>
          <Text style={styles.saveText}>{saving ? 'Saving…' : isNote ? 'Add note' : 'Add assignment'}</Text>
        </Pressable>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  uploadSection: { marginTop: 4, marginBottom: 12 },
  uploadLabel: { fontSize: 13, fontWeight: '700', marginBottom: 10 },
  uploadRow: { flexDirection: 'row', gap: 10 },
  uploadTile: {
    flex: 1,
    borderRadius: 14,
    paddingVertical: 16,
    alignItems: 'center',
    gap: 6,
    borderWidth: 1,
  },
  uploadTileText: { fontSize: 12, fontWeight: '700', color: PRIMARY },
  filesHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 14,
    marginBottom: 8,
  },
  filesCount: { fontSize: 13, fontWeight: '700' },
  clearAll: { fontSize: 13, fontWeight: '700', color: '#dc2626' },
  previewRow: { gap: 10, paddingVertical: 4 },
  previewCard: {
    width: 140,
    borderRadius: 14,
    padding: 10,
    borderWidth: 1,
    position: 'relative',
  },
  removeBtn: {
    position: 'absolute',
    top: 4,
    right: 4,
    zIndex: 2,
    backgroundColor: 'rgba(255,255,255,0.9)',
    borderRadius: 11,
  },
  previewImage: { width: '100%', height: 100, borderRadius: 10 },
  fileIconWrap: {
    width: '100%',
    height: 100,
    borderRadius: 10,
    alignItems: 'center',
    justifyContent: 'center',
  },
  fileName: { marginTop: 8, fontSize: 12, textAlign: 'center' },
  uploadHint: { marginTop: 12, fontSize: 12, textAlign: 'center' },
  saveBtn: {
    backgroundColor: PRIMARY,
    paddingVertical: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 8,
  },
  disabled: { opacity: 0.6 },
  saveText: { color: '#fff', fontWeight: '700', fontSize: 16 },
});
