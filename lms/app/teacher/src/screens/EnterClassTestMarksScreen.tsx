import React, { useEffect, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View, TextInput } from 'react-native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export default function EnterClassTestMarksScreen() {
  const [sessions, setSessions] = useState<{ label: string; value: string }[]>([]);
  const [courses, setCourses] = useState<{ label: string; value: string }[]>([]);
  const [subjects, setSubjects] = useState<{ label: string; value: string }[]>([]);
  const [tests, setTests] = useState<{ label: string; value: string }[]>([]);
  const [batches, setBatches] = useState<{ label: string; value: string }[]>([]);

  const [session, setSession] = useState('');
  const [courseId, setCourseId] = useState('');
  const [subjectId, setSubjectId] = useState('');
  const [testId, setTestId] = useState('');
  const [batch, setBatch] = useState('');

  const [testMeta, setTestMeta] = useState<any>(null);
  const [students, setStudents] = useState<any[]>([]);
  const [marks, setMarks] = useState<Record<string, string>>({});
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    LmsApi.formSessions().then((s: any) =>
      setSessions((s.sessions ?? []).map((x: any) => ({ label: x.session_name, value: x.session_name }))),
    );
    LmsApi.ctCourses().then((res: any) =>
      setCourses((res.courses ?? []).map((c: any) => ({ label: c.course_name, value: String(c.id) }))),
    );
  }, []);

  useEffect(() => {
    if (!courseId) {
      setSubjects([]);
      setTests([]);
      return;
    }
    LmsApi.ctSubjects(Number(courseId)).then((res: any) =>
      setSubjects((res.subjects ?? []).map((s: any) => ({ label: s.subject_name, value: String(s.id) }))),
    );
    const cn = courses.find(c => c.value === courseId)?.label;
    if (cn) {
      LmsApi.formBatches(cn).then((b: any) =>
        setBatches([
          { label: 'All batches', value: '' },
          ...(b.batches ?? []).map((name: string) => ({ label: name, value: name })),
        ]),
      );
    }
  }, [courseId, courses]);

  useEffect(() => {
    if (!courseId || !subjectId) {
      setTests([]);
      return;
    }
    LmsApi.ctTests(Number(courseId), Number(subjectId)).then((res: any) =>
      setTests(
        (res.tests ?? []).map((t: any) => ({
          label: `${t.test_name} (${t.test_date})`,
          value: String(t.id),
        })),
      ),
    );
  }, [courseId, subjectId]);

  async function loadStudents() {
    if (!testId) {
      Alert.alert('Select a test');
      return;
    }
    setLoading(true);
    try {
      const res: any = await LmsApi.ctStudentsMarks({
        test_id: Number(testId),
        session_name: session || undefined,
        batch: batch || undefined,
      });
      setTestMeta(res.test);
      setStudents(res.students ?? []);
      const m: Record<string, string> = {};
      (res.students ?? []).forEach((st: any) => {
        m[String(st.student_id)] =
          st.marks_obtained != null ? String(st.marks_obtained) : '';
      });
      setMarks(m);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Load failed');
    } finally {
      setLoading(false);
    }
  }

  async function save() {
    if (!testId) return;
    setSaving(true);
    try {
      await LmsApi.saveClassTestMarks({ test_id: Number(testId), marks });
      Alert.alert('Success', 'Marks saved');
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Save failed');
    } finally {
      setSaving(false);
    }
  }

  return (
    <ScreenLayout title="Enter Test Marks" subtitle="Class test marks">
      <Card>
        <FormPicker label="Session (optional filter)" value={session} options={[{ label: 'Any', value: '' }, ...sessions]} onChange={setSession} />
        <FormPicker label="Course" value={courseId} options={courses} onChange={v => { setCourseId(v); setSubjectId(''); setTestId(''); }} />
        <FormPicker label="Subject" value={subjectId} options={subjects} onChange={v => { setSubjectId(v); setTestId(''); }} disabled={!courseId} />
        <FormPicker label="Test" value={testId} options={tests} onChange={setTestId} disabled={!subjectId} />
        <FormPicker label="Batch filter" value={batch} options={batches} onChange={setBatch} disabled={!courseId} />
        <Pressable style={styles.btnOutline} onPress={loadStudents} disabled={loading}>
          <Text style={styles.btnOutlineText}>{loading ? 'Loading…' : 'Load students'}</Text>
        </Pressable>
      </Card>

      {testMeta ? (
        <Card title={testMeta.test_name}>
          <Text style={styles.meta}>Max marks: {testMeta.total_marks}</Text>
          {students.map((st: any) => (
            <View key={st.student_id} style={styles.row}>
              <Text style={styles.name}>{st.name}</Text>
              <TextInput
                style={styles.marksInput}
                keyboardType="numeric"
                placeholder="Marks"
                value={marks[String(st.student_id)] ?? ''}
                onChangeText={t => setMarks(m => ({ ...m, [String(st.student_id)]: t }))}
              />
            </View>
          ))}
          <Pressable style={[styles.btn, saving && styles.disabled]} onPress={save} disabled={saving}>
            <Text style={styles.btnText}>{saving ? 'Saving…' : 'Save marks'}</Text>
          </Pressable>
        </Card>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  btn: { backgroundColor: PRIMARY, padding: 14, borderRadius: 10, alignItems: 'center', marginTop: 12 },
  btnText: { color: '#fff', fontWeight: '700' },
  btnOutline: { borderWidth: 1, borderColor: PRIMARY, padding: 12, borderRadius: 10, alignItems: 'center' },
  btnOutlineText: { color: PRIMARY, fontWeight: '600' },
  disabled: { opacity: 0.6 },
  meta: { color: theme.muted, marginBottom: 12 },
  row: { flexDirection: 'row', alignItems: 'center', marginBottom: 10, gap: 8 },
  name: { flex: 1, fontSize: 14, fontWeight: '600' },
  marksInput: {
    width: 80,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 8,
    padding: 8,
    textAlign: 'center',
  },
});
