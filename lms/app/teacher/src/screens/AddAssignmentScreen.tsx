import React, { useEffect, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import { FormInput } from '../components/FormInput';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

type AssignType = 'link' | 'file';

export default function AddAssignmentScreen() {
  const navigation = useNavigation();
  const [batches, setBatches] = useState<{ label: string; value: string }[]>([]);
  const [type, setType] = useState<AssignType>('link');
  const [batch, setBatch] = useState('');
  const [documentName, setDocumentName] = useState('');
  const [link, setLink] = useState('');
  const [file, setFile] = useState<{ uri: string; name: string; type: string } | null>(null);
  const [saving, setSaving] = useState(false);

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

  async function pickFile() {
    try {
      const { pick, types } = await import('@react-native-documents/picker');
      const results = await pick({ type: [types.pdf, types.plainText, types.allFiles] });
      const picked = results[0];
      if (!picked?.uri) {
        return;
      }
      setFile({
        uri: picked.uri,
        name: picked.name ?? 'assignment.pdf',
        type: picked.type ?? 'application/octet-stream',
      });
    } catch (e: any) {
      const mod = await import('@react-native-documents/picker');
      if (mod.isErrorWithCode(e) && e.code === mod.errorCodes.OPERATION_CANCELED) {
        return;
      }
      Alert.alert('File picker', e?.message ?? 'Could not pick file');
    }
  }

  async function save() {
    if (!batch || !documentName.trim()) {
      Alert.alert('Required', 'Select batch and enter document name');
      return;
    }
    setSaving(true);
    try {
      if (type === 'link') {
        if (!link.trim()) {
          Alert.alert('Required', 'Enter assignment link');
          return;
        }
        await LmsApi.createAssignmentLink({
          type: 'link',
          batch_name: batch,
          document_name: documentName.trim(),
          link: link.trim(),
        });
      } else {
        if (!file) {
          Alert.alert('Required', 'Pick a file to upload');
          return;
        }
        const form = new FormData();
        form.append('type', 'file');
        form.append('batch_name', batch);
        form.append('document_name', documentName.trim());
        form.append('file', {
          uri: file.uri,
          name: file.name,
          type: file.type,
        } as any);
        await LmsApi.createAssignmentFile(form);
      }
      Alert.alert('Success', 'Assignment added', [
        { text: 'OK', onPress: () => navigation.navigate('AssignmentsList') },
      ]);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not save assignment');
    } finally {
      setSaving(false);
    }
  }

  return (
    <ScreenLayout
      title="Add Assignment"
      subtitle="Link or file for a batch"
      onBack={() => navigation.goBack()}>
      <Card>
        <FormPicker
          label="Type"
          value={type}
          options={[
            { label: 'Link', value: 'link' },
            { label: 'File', value: 'file' },
          ]}
          onChange={v => setType(v as AssignType)}
        />
        <FormPicker label="Batch" value={batch} options={batches} onChange={setBatch} />
        <FormInput
          label="Document name"
          value={documentName}
          onChangeText={setDocumentName}
          placeholder="e.g. DBMS Chapter 1"
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
          <View style={styles.fileRow}>
            <Pressable style={styles.pickBtn} onPress={pickFile}>
              <Text style={styles.pickText}>{file ? 'Change file' : 'Pick file'}</Text>
            </Pressable>
            {file ? <Text style={styles.fileName}>{file.name}</Text> : null}
          </View>
        )}
        <Pressable style={[styles.saveBtn, saving && styles.disabled]} onPress={save} disabled={saving}>
          <Text style={styles.saveText}>{saving ? 'Saving…' : 'Add assignment'}</Text>
        </Pressable>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  fileRow: { marginTop: 8, marginBottom: 12 },
  pickBtn: {
    backgroundColor: PRIMARY,
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  pickText: { color: '#fff', fontWeight: '700' },
  fileName: { marginTop: 8, fontSize: 13, color: theme.muted },
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
