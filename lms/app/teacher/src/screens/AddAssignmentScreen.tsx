import React, { useEffect, useMemo, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View, Image, ScrollView, ActivityIndicator } from 'react-native';
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
  normalizeUploadFile,
  pickImagesFromGallery,
  takePhoto,
  type PickedFile,
} from '../utils/mediaPicker';

type AssignType = 'link' | 'file';
type ContentKind = 'assignment' | 'note';
type ScreenName = 'AddAssignment' | 'AddNote' | 'EditAssignment' | 'EditNote';

export default function AddAssignmentScreen() {
  const navigation = useNavigation();
  const route = useRoute<RouteProp<WorkStackParamList, ScreenName>>();
  const colors = useThemeColors();
  const isEdit = route.name === 'EditAssignment' || route.name === 'EditNote';
  const editId = isEdit ? route.params.id : undefined;
  const contentKind: ContentKind =
    route.name === 'AddNote' || route.name === 'EditNote' || route.params?.contentKind === 'note'
      ? 'note'
      : 'assignment';
  const isNote = contentKind === 'note';

  const [loading, setLoading] = useState(isEdit);
  const [batches, setBatches] = useState<{ label: string; value: string }[]>([]);
  const [subjects, setSubjects] = useState<{ label: string; value: string }[]>([]);
  const [type, setType] = useState<AssignType>(isNote ? 'file' : 'link');
  const [batch, setBatch] = useState('');
  const [subjectId, setSubjectId] = useState('');
  const [documentName, setDocumentName] = useState('');
  const [link, setLink] = useState('');
  const [files, setFiles] = useState<PickedFile[]>([]);
  const [currentFileName, setCurrentFileName] = useState('');
  const [saving, setSaving] = useState(false);
  const [uploadProgress, setUploadProgress] = useState('');

  const screenTitle = useMemo(() => {
    if (isEdit) return isNote ? 'Edit Note' : 'Edit Assignment';
    return isNote ? 'Add Note' : 'Add Assignment';
  }, [isEdit, isNote]);

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
    if (!isEdit || !editId) return;
    setLoading(true);
    LmsApi.assignment(editId)
      .then((res: any) => {
        const item = res.assignment;
        if (!item) return;
        const itemType = String(item.type ?? 'file').toLowerCase() === 'link' ? 'link' : 'file';
        setType(itemType);
        setBatch(String(item.batch_name ?? ''));
        setSubjectId(item.subject_id != null ? String(item.subject_id) : '');
        setDocumentName(String(item.document_name ?? ''));
        if (itemType === 'link') {
          setLink(String(item.link_url ?? item.document ?? ''));
        } else {
          setCurrentFileName(String(item.document ?? ''));
        }
      })
      .catch((e: any) => Alert.alert('Error', e?.message ?? 'Could not load'))
      .finally(() => setLoading(false));
  }, [editId, isEdit]);

  useEffect(() => {
    if (!batch) {
      setSubjects([]);
      if (!isEdit) setSubjectId('');
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
  }, [batch, isEdit]);

  function addFiles(picked: PickedFile[]) {
    if (!picked.length) return;
    const normalized = picked.map(normalizeUploadFile);
    setFiles(prev => {
      const merged = isEdit ? [normalized[0]] : [...prev, ...normalized];
      const seen = new Set<string>();
      return merged.filter(file => {
        const id = `${file.uri}|${file.name}`;
        if (seen.has(id)) return false;
        seen.add(id);
        return true;
      });
    });
  }

  function removeFile(key: string) {
    setFiles(prev => prev.filter(file => file.key !== key));
  }

  function clearFiles() {
    setFiles([]);
  }

  async function pickFile() {
    try {
      const { pick, types, keepLocalCopy } = await import('@react-native-documents/picker');
      const results = await pick({
        type: [types.pdf, types.plainText, types.images, types.allFiles],
        allowMultiSelection: !isEdit,
      });
      const withUri = results.filter(r => r.uri);
      if (!withUri.length) return;

      const copyInputs = withUri.map((r, index) => ({
        uri: r.uri!,
        fileName: r.name ?? (isNote ? `note-${index + 1}.pdf` : `assignment-${index + 1}.pdf`),
        ...(r.convertibleToMimeTypes?.[0]
          ? { convertVirtualFileToType: r.convertibleToMimeTypes[0] }
          : {}),
      }));

      const copies = await keepLocalCopy({
        destination: 'cachesDirectory',
        files: copyInputs as [(typeof copyInputs)[0], ...typeof copyInputs],
      });

      const picked = withUri.map((r, index) => {
        const copy = copies[index];
        const uri =
          copy?.status === 'success' && copy.localUri ? copy.localUri : (r.uri as string);
        const name = r.name ?? (isNote ? `note-${index + 1}.pdf` : `assignment-${index + 1}.pdf`);
        return normalizeUploadFile({
          uri,
          name,
          type: r.type ?? r.nativeType ?? 'application/octet-stream',
          key: `${uri}-${name}-${Date.now()}-${index}`,
        });
      });
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

  async function uploadFiles(
    items: PickedFile[],
    baseTitle: string,
    subject: { label: string; value: string } | undefined,
  ) {
    setUploadProgress(`Preparing ${items.length} file${items.length === 1 ? '' : 's'}…`);
    const result = (await LmsApi.createAssignmentFilesUpload(
      items,
      {
        content_kind: contentKind,
        batch_name: batch,
        document_name: baseTitle,
        subject_id: Number(subjectId),
        subject_name: subject?.label,
      },
      (current, total) => {
        setUploadProgress(`Uploading ${current} of ${total}…`);
      },
    )) as { message?: string; count?: number; file_count?: number };
    return result;
  }

  async function saveEdit(subject: { label: string; value: string } | undefined) {
    if (type === 'link') {
      await LmsApi.updateAssignment(editId!, {
        batch_name: batch,
        document_name: documentName.trim(),
        subject_id: Number(subjectId),
        subject_name: subject?.label,
        link: link.trim(),
      });
    } else if (files.length) {
      await LmsApi.updateAssignmentFileUpload(editId!, files[0], {
        batch_name: batch,
        document_name: documentName.trim(),
        subject_id: Number(subjectId),
        subject_name: subject?.label,
      });
    } else {
      await LmsApi.updateAssignment(editId!, {
        batch_name: batch,
        document_name: documentName.trim(),
        subject_id: Number(subjectId),
        subject_name: subject?.label,
      });
    }
    Alert.alert('Success', isNote ? 'Note updated' : 'Assignment updated', [
      { text: 'OK', onPress: () => navigation.goBack() },
    ]);
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
    setUploadProgress('');
    try {
      const subject = subjects.find(s => s.value === subjectId);

      if (isEdit) {
        if (type === 'link' && !link.trim()) {
          Alert.alert('Required', 'Enter link URL');
          return;
        }
        await saveEdit(subject);
        return;
      }

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
        if (files.length > 20) {
          Alert.alert('Too many files', 'You can upload up to 20 files at a time.');
          return;
        }
        const baseTitle = documentName.trim();
        const result = await uploadFiles(files, baseTitle, subject);
        const successMessage =
          result?.message ??
          (files.length > 1
            ? `${isNote ? 'Note' : 'Assignment'} added with ${files.length} files`
            : isNote
              ? 'Note added'
              : 'Assignment added');
        Alert.alert('Success', successMessage, [
          { text: 'OK', onPress: () => navigation.navigate('WorkHub' as never) },
        ]);
        return;
      }
      Alert.alert('Success', isNote ? 'Note added' : 'Assignment added', [
        { text: 'OK', onPress: () => navigation.navigate('WorkHub' as never) },
      ]);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not save');
    } finally {
      setSaving(false);
      setUploadProgress('');
    }
  }

  if (loading) {
    return (
      <ScreenLayout title={screenTitle} subtitle="Loading…" onBack={() => navigation.goBack()}>
        <ActivityIndicator color={PRIMARY} style={styles.loader} />
      </ScreenLayout>
    );
  }

  return (
    <ScreenLayout
      title={screenTitle}
      subtitle={isNote ? 'Upload class notes by subject' : 'Share homework by subject'}
      onBack={() => navigation.goBack()}>
      <Card>
        {!isNote && !isEdit ? (
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
            <Text style={[styles.uploadLabel, { color: colors.muted }]}>
              {isEdit ? 'Replace file (optional)' : 'Upload photo or file'}
            </Text>
            {isEdit && currentFileName && !files.length ? (
              <Text style={[styles.currentFile, { color: colors.text }]}>Current: {currentFileName}</Text>
            ) : null}
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
                  <Text style={styles.clearAll}>Clear</Text>
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
                {isEdit
                  ? 'Leave empty to keep the current file, or pick a new one to replace it.'
                  : 'Tap Camera, Gallery, or File. Add multiple items (up to 20) and upload together.'}
              </Text>
            )}
          </View>
        )}
        <Pressable style={[styles.saveBtn, saving && styles.disabled]} onPress={save} disabled={saving}>
          <Text style={styles.saveText}>
            {saving
              ? uploadProgress || 'Saving…'
              : isEdit
                ? 'Save changes'
                : isNote
                  ? files.length > 1
                    ? `Add note (${files.length} files)`
                    : 'Add note'
                  : type === 'file' && files.length > 1
                    ? `Add assignment (${files.length} files)`
                    : 'Add assignment'}
          </Text>
        </Pressable>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  loader: { marginTop: 24 },
  uploadSection: { marginTop: 4, marginBottom: 12 },
  uploadLabel: { fontSize: 13, fontWeight: '700', marginBottom: 10 },
  currentFile: { fontSize: 13, marginBottom: 10, fontWeight: '600' },
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
