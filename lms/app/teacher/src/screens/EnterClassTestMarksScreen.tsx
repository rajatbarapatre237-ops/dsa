import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import {
  Text,
  Pressable,
  StyleSheet,
  Alert,
  View,
  TextInput,
  FlatList,
  ActivityIndicator,
  Platform,
} from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { useThemeColors, textInputStyle } from '../ui/useThemeColors';
import { platformWeight } from '../ui/typography';
import { WorkStackParamList } from '../navigation/types';

type StudentRow = {
  student_id: number;
  name: string;
  marks_obtained?: number | null;
};

const StudentMarkRow = React.memo(function StudentMarkRow({
  studentId,
  name,
  initialMark,
  inputKey,
  onMarkChange,
  isLast,
}: {
  studentId: number;
  name: string;
  initialMark: string;
  inputKey: string;
  onMarkChange: (id: number, value: string) => void;
  isLast?: boolean;
}) {
  const colors = useThemeColors();
  return (
    <View style={[styles.row, isLast && styles.rowLast]}>
      <Text style={[styles.name, platformWeight('600'), { color: colors.text }]} numberOfLines={1}>
        {name}
      </Text>
      <TextInput
        key={inputKey}
        style={[textInputStyle(colors), styles.marksInput]}
        keyboardType="numeric"
        placeholder="Marks"
        placeholderTextColor={colors.muted}
        defaultValue={initialMark}
        onChangeText={value => onMarkChange(studentId, value)}
      />
    </View>
  );
});

export default function EnterClassTestMarksScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();
  const route = useRoute<RouteProp<WorkStackParamList, 'EnterMarks'>>();
  const preset = route.params;

  const [sessions, setSessions] = useState<{ label: string; value: string }[]>([]);
  const [courses, setCourses] = useState<{ label: string; value: string }[]>([]);
  const [subjects, setSubjects] = useState<{ label: string; value: string }[]>([]);
  const [tests, setTests] = useState<{ label: string; value: string }[]>([]);
  const [batches, setBatches] = useState<{ label: string; value: string }[]>([]);

  const [session, setSession] = useState('');
  const [courseId, setCourseId] = useState(preset?.courseId ?? '');
  const [subjectId, setSubjectId] = useState(preset?.subjectId ?? '');
  const [testId, setTestId] = useState(preset?.testId ?? '');
  const [batch, setBatch] = useState('');

  const [testMeta, setTestMeta] = useState<any>(null);
  const [students, setStudents] = useState<StudentRow[]>([]);
  const [marksVersion, setMarksVersion] = useState(0);
  const marksRef = useRef<Record<string, string>>({});
  const initialMarksRef = useRef<Record<string, string>>({});

  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const prevSession = useRef<string | null>(null);

  const handleMarkChange = useCallback((studentId: number, value: string) => {
    marksRef.current[String(studentId)] = value;
  }, []);

  useEffect(() => {
    (async () => {
      try {
        const s: any = await LmsApi.formSessions();
        setSessions(
          (s.sessions ?? []).map((x: any) => ({ label: x.session_name, value: x.session_name })),
        );
      } catch {
        // ignore
      }
    })();
  }, []);

  useEffect(() => {
    (async () => {
      try {
        const res: any = await LmsApi.ctCourses(session || undefined);
        setCourses((res.courses ?? []).map((c: any) => ({ label: c.course_name, value: String(c.id) })));
      } catch (e: any) {
        Alert.alert('Error', e?.message ?? 'Could not load courses');
        setCourses([]);
      }
    })();

    if (prevSession.current !== null && prevSession.current !== session) {
      setCourseId('');
      setSubjectId('');
      setTestId('');
      setSubjects([]);
      setTests([]);
      setTestMeta(null);
      setStudents([]);
    }
    prevSession.current = session;
  }, [session]);

  useEffect(() => {
    if (!courseId) {
      setSubjects([]);
      setTests([]);
      return;
    }
    (async () => {
      try {
        const res: any = await LmsApi.ctSubjects(Number(courseId));
        setSubjects((res.subjects ?? []).map((s: any) => ({ label: s.subject_name, value: String(s.id) })));
      } catch (e: any) {
        Alert.alert('Error', e?.message ?? 'Could not load subjects');
        setSubjects([]);
      }
    })();
    const cn = courses.find(c => c.value === courseId)?.label;
    if (cn) {
      LmsApi.formBatches(cn)
        .then((b: any) =>
          setBatches([
            { label: 'All batches', value: '' },
            ...(b.batches ?? []).map((name: string) => ({ label: name, value: name })),
          ]),
        )
        .catch(() => setBatches([{ label: 'All batches', value: '' }]));
    }
  }, [courseId, courses]);

  useEffect(() => {
    if (!courseId || !subjectId) {
      setTests([]);
      return;
    }
    (async () => {
      try {
        const res: any = await LmsApi.ctTests(Number(courseId), Number(subjectId));
        setTests(
          (res.tests ?? []).map((t: any) => ({
            label: `${t.test_name} (${t.test_date})`,
            value: String(t.id),
          })),
        );
      } catch (e: any) {
        Alert.alert('Error', e?.message ?? 'Could not load tests');
        setTests([]);
      }
    })();
  }, [courseId, subjectId]);

  const loadStudents = useCallback(async (selectedTestId?: string) => {
    const id = selectedTestId ?? testId;
    if (!id) {
      Alert.alert('Select a test');
      return;
    }
    setLoading(true);
    setStudents([]);
    setTestMeta(null);
    try {
      const res: any = await LmsApi.ctStudentsMarks({
        test_id: Number(id),
        session_name: session || undefined,
        batch: batch || undefined,
      });
      const list: StudentRow[] = res.students ?? [];
      const initial: Record<string, string> = {};
      list.forEach(st => {
        initial[String(st.student_id)] =
          st.marks_obtained != null ? String(st.marks_obtained) : '';
      });
      marksRef.current = { ...initial };
      initialMarksRef.current = initial;
      setTestMeta(res.test);
      setMarksVersion(v => v + 1);
      setStudents(list);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Load failed');
    } finally {
      setLoading(false);
    }
  }, [testId, session, batch]);

  useEffect(() => {
    if (preset?.testId && preset.courseId && preset.subjectId) {
      loadStudents(preset.testId);
    }
  }, [preset?.testId, preset?.courseId, preset?.subjectId, loadStudents]);

  async function save() {
    if (!testId) return;
    setSaving(true);
    try {
      await LmsApi.saveClassTestMarks({ test_id: Number(testId), marks: marksRef.current });
      Alert.alert('Success', 'Marks saved', [
        { text: 'OK', onPress: () => navigation.navigate('WorkHub') },
      ]);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Save failed');
    } finally {
      setSaving(false);
    }
  }

  const clearStudents = useCallback(() => {
    setTestMeta(null);
    setStudents([]);
    marksRef.current = {};
    initialMarksRef.current = {};
  }, []);

  const listHeader = useMemo(
    () => (
      <View>
        <Card>
          <Text style={styles.hint}>Select the test, load students, then enter marks for each student.</Text>
          <FormPicker
            label="Session (optional filter)"
            value={session}
            options={[{ label: 'Any', value: '' }, ...sessions]}
            onChange={setSession}
          />
          <FormPicker
            label="Course"
            value={courseId}
            options={courses}
            onChange={v => {
              setCourseId(v);
              setSubjectId('');
              setTestId('');
              clearStudents();
            }}
          />
          <FormPicker
            label="Subject"
            value={subjectId}
            options={subjects}
            onChange={v => {
              setSubjectId(v);
              setTestId('');
              clearStudents();
            }}
            disabled={!courseId}
          />
          <FormPicker
            label="Test"
            value={testId}
            options={tests}
            onChange={v => {
              setTestId(v);
              clearStudents();
            }}
            disabled={!subjectId}
          />
          <FormPicker
            label="Batch filter"
            value={batch}
            options={batches}
            onChange={setBatch}
            disabled={!courseId}
          />
          <Pressable style={styles.btnOutline} onPress={() => loadStudents()} disabled={loading}>
            {loading ? (
              <ActivityIndicator color={PRIMARY} />
            ) : (
              <Text style={[styles.btnOutlineText, platformWeight('600')]}>Load students</Text>
            )}
          </Pressable>
        </Card>

        {testMeta ? (
          <Card title={testMeta.test_name}>
            <Text style={styles.meta}>
              Max marks: {testMeta.total_marks}
              {students.length > 0 ? ` · ${students.length} students` : ''}
            </Text>
          </Card>
        ) : null}
      </View>
    ),
    [
      session,
      sessions,
      courseId,
      courses,
      subjectId,
      subjects,
      testId,
      tests,
      batch,
      batches,
      loading,
      testMeta,
      students.length,
      loadStudents,
      clearStudents,
    ],
  );

  const listFooter = useMemo(() => {
    if (!testMeta || students.length === 0) {
      if (testMeta && students.length === 0 && !loading) {
        return (
          <Card>
            <Text style={styles.empty}>No students found for this test.</Text>
          </Card>
        );
      }
      return <View style={styles.listFooterSpace} />;
    }

    return (
      <View style={styles.footerWrap}>
        <Pressable
          style={[styles.btn, saving && styles.disabled]}
          onPress={save}
          disabled={saving}>
          <Text style={[styles.btnText, platformWeight('700')]}>
            {saving ? 'Saving…' : 'Save marks'}
          </Text>
        </Pressable>
        <View style={styles.listFooterSpace} />
      </View>
    );
  }, [testMeta, students.length, loading, saving]);

  const renderStudent = useCallback(
    ({ item, index }: { item: StudentRow; index: number }) => (
      <View
        style={[
          styles.studentShell,
          index === 0 && styles.studentShellTop,
          index === students.length - 1 && styles.studentShellBottom,
        ]}>
        <StudentMarkRow
          studentId={item.student_id}
          name={item.name}
          initialMark={initialMarksRef.current[String(item.student_id)] ?? ''}
          inputKey={`${item.student_id}-${marksVersion}`}
          onMarkChange={handleMarkChange}
          isLast={index === students.length - 1}
        />
      </View>
    ),
    [marksVersion, handleMarkChange, students.length],
  );

  return (
    <ScreenLayout
      title="Enter Test Marks"
      subtitle="Record scores for a class test"
      onBack={() => navigation.navigate('WorkHub')}
      scroll={false}>
      <FlatList
        style={styles.flex}
        data={students}
        keyExtractor={item => String(item.student_id)}
        renderItem={renderStudent}
        ListHeaderComponent={listHeader}
        ListFooterComponent={listFooter}
        contentContainerStyle={styles.listContent}
        keyboardShouldPersistTaps="handled"
        initialNumToRender={18}
        maxToRenderPerBatch={24}
        windowSize={9}
        removeClippedSubviews={Platform.OS === 'android'}
        getItemLayout={(_, index) => ({
          length: STUDENT_ROW_HEIGHT,
          offset: STUDENT_ROW_HEIGHT * index,
          index,
        })}
      />
    </ScreenLayout>
  );
}

const STUDENT_ROW_HEIGHT = 46;

const styles = StyleSheet.create({
  flex: { flex: 1 },
  listContent: {
    padding: 16,
    paddingBottom: 100,
  },
  hint: {
    fontSize: 13,
    color: theme.muted,
    lineHeight: 18,
    marginBottom: 14,
  },
  btn: { backgroundColor: PRIMARY, padding: 14, borderRadius: 10, alignItems: 'center' },
  btnText: { color: '#fff' },
  btnOutline: {
    borderWidth: 1,
    borderColor: PRIMARY,
    padding: 12,
    borderRadius: 10,
    alignItems: 'center',
    minHeight: 44,
    justifyContent: 'center',
  },
  btnOutlineText: { color: PRIMARY },
  disabled: { opacity: 0.6 },
  meta: { color: theme.muted },
  studentShell: {
    backgroundColor: theme.card,
    borderLeftWidth: 1,
    borderRightWidth: 1,
    borderColor: theme.border,
  },
  studentShellTop: {
    borderTopWidth: 1,
    borderTopLeftRadius: 18,
    borderTopRightRadius: 18,
    marginTop: -14,
  },
  studentShellBottom: {
    borderBottomWidth: 1,
    borderBottomLeftRadius: 18,
    borderBottomRightRadius: 18,
    marginBottom: 4,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    height: STUDENT_ROW_HEIGHT,
    paddingHorizontal: 16,
    gap: 8,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  rowLast: { borderBottomWidth: 0 },
  name: { flex: 1, fontSize: 14, color: theme.text },
  marksInput: {
    width: 80,
    paddingVertical: 8,
    paddingHorizontal: 8,
    textAlign: 'center',
    fontSize: 14,
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 8,
  },
  footerWrap: { marginTop: 12 },
  listFooterSpace: { height: 24 },
});
