import React, { useCallback, useEffect, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import { FormDateInput } from '../components/FormDateInput';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export default function AddAttendanceScreen() {
  const navigation = useNavigation<any>();
  const [sessions, setSessions] = useState<{ label: string; value: string }[]>([]);
  const [courses, setCourses] = useState<{ label: string; value: string }[]>([]);
  const [batches, setBatches] = useState<{ label: string; value: string }[]>([]);
  const [students, setStudents] = useState<any[]>([]);

  const [session, setSession] = useState('');
  const [course, setCourse] = useState('');
  const [batch, setBatch] = useState('');
  const [date, setDate] = useState(new Date().toISOString().slice(0, 10));
  const [statuses, setStatuses] = useState<Record<string, 'present' | 'absent'>>({});
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        const s: any = await LmsApi.formSessions();
        setSessions(
          (s.sessions ?? []).map((x: any) => ({
            label: x.session_name,
            value: x.session_name,
          })),
        );
        const c: any = await LmsApi.formCourses();
        setCourses((c.courses ?? []).map((name: string) => ({ label: name, value: name })));
      } catch {
        /* ignore */
      }
    })();
  }, []);

  useEffect(() => {
    if (!course) {
      setBatches([]);
      return;
    }
    LmsApi.formBatches(course)
      .then((b: any) => {
        setBatches((b.batches ?? []).map((name: string) => ({ label: name, value: name })));
      })
      .catch(() => setBatches([]));
  }, [course]);

  const loadStudents = useCallback(async () => {
    if (!course || !session || !batch) {
      Alert.alert('Select course, session, and batch first');
      return;
    }
    setLoading(true);
    try {
      const res: any = await LmsApi.formStudents({ course, session, batch });
      const list = res.students ?? [];
      setStudents(list);
      const init: Record<string, 'present' | 'absent'> = {};
      list.forEach((st: any) => {
        init[String(st.id)] = 'present';
      });
      setStatuses(init);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not load students');
    } finally {
      setLoading(false);
    }
  }, [course, session, batch]);

  async function save() {
    if (!students.length) {
      Alert.alert('Load students first');
      return;
    }
    setSaving(true);
    try {
      const records = students.map((st: any) => ({
        student_id: String(st.id),
        status: statuses[String(st.id)] ?? 'present',
      }));
      await LmsApi.saveAttendance({ date, course, batch, records });
      Alert.alert('Success', 'Attendance recorded');
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Save failed');
    } finally {
      setSaving(false);
    }
  }

  return (
    <ScreenLayout
      title="Add Attendance"
      subtitle="Mark present / absent"
      onBack={() => navigation.navigate('AttendanceHub')}>
      <Card>
        <FormDateInput label="Date" value={date} onChange={setDate} />
        <FormPicker label="Session" value={session} options={sessions} onChange={setSession} />
        <FormPicker label="Course" value={course} options={courses} onChange={v => { setCourse(v); setBatch(''); }} />
        <FormPicker label="Batch" value={batch} options={batches} onChange={setBatch} disabled={!course} />
        <Pressable style={styles.btnOutline} onPress={loadStudents} disabled={loading}>
          <Text style={styles.btnOutlineText}>{loading ? 'Loading…' : 'Load students'}</Text>
        </Pressable>
      </Card>

      {students.length > 0 ? (
        <Card title={`Students (${students.length})`}>
          {students.map((st: any) => (
            <View key={st.id} style={styles.row}>
              <Text style={styles.name}>{st.name}</Text>
              <View style={styles.toggleRow}>
                <Pressable
                  style={[styles.chip, statuses[String(st.id)] === 'present' && styles.chipOn]}
                  onPress={() => setStatuses(s => ({ ...s, [String(st.id)]: 'present' }))}>
                  <Text style={[styles.chipText, statuses[String(st.id)] === 'present' && styles.chipTextOn]}>
                    Present
                  </Text>
                </Pressable>
                <Pressable
                  style={[styles.chip, statuses[String(st.id)] === 'absent' && styles.chipAbsent]}
                  onPress={() => setStatuses(s => ({ ...s, [String(st.id)]: 'absent' }))}>
                  <Text style={[styles.chipText, statuses[String(st.id)] === 'absent' && styles.chipTextOn]}>
                    Absent
                  </Text>
                </Pressable>
              </View>
            </View>
          ))}
          <Pressable style={[styles.btn, saving && styles.disabled]} onPress={save} disabled={saving}>
            <Text style={styles.btnText}>{saving ? 'Saving…' : 'Save attendance'}</Text>
          </Pressable>
        </Card>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  btn: { backgroundColor: PRIMARY, padding: 14, borderRadius: 10, alignItems: 'center', marginTop: 12 },
  btnText: { color: '#fff', fontWeight: '700' },
  btnOutline: { borderWidth: 1, borderColor: PRIMARY, padding: 12, borderRadius: 10, alignItems: 'center', marginBottom: 8 },
  btnOutlineText: { color: PRIMARY, fontWeight: '600' },
  disabled: { opacity: 0.6 },
  row: { marginBottom: 14, paddingBottom: 12, borderBottomWidth: 1, borderBottomColor: '#eee' },
  name: { fontSize: 15, fontWeight: '600', marginBottom: 8 },
  toggleRow: { flexDirection: 'row', gap: 8 },
  chip: { flex: 1, padding: 10, borderRadius: 8, backgroundColor: '#f1f5f9', alignItems: 'center' },
  chipOn: { backgroundColor: PRIMARY },
  chipAbsent: { backgroundColor: '#dc3545' },
  chipText: { color: theme.text, fontWeight: '600', fontSize: 13 },
  chipTextOn: { color: '#fff' },
});
