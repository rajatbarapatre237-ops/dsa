import React, { useEffect, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View, ActivityIndicator } from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import { FormInput } from '../components/FormInput';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { WorkStackParamList } from '../navigation/types';

export default function CreateClassTestScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();
  const route = useRoute<RouteProp<WorkStackParamList, 'CreateClassTest'>>();
  const editTestId = route.params?.testId ? Number(route.params.testId) : undefined;
  const isEdit = editTestId != null && !Number.isNaN(editTestId);

  const [loading, setLoading] = useState(isEdit);
  const [courses, setCourses] = useState<{ label: string; value: string }[]>([]);
  const [subjects, setSubjects] = useState<{ label: string; value: string }[]>([]);

  const [courseId, setCourseId] = useState('');
  const [subjectId, setSubjectId] = useState('');
  const [testName, setTestName] = useState('');
  const [testDate, setTestDate] = useState(new Date().toISOString().slice(0, 10));
  const [totalMarks, setTotalMarks] = useState('100');
  const [passingMarks, setPassingMarks] = useState('40');
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    LmsApi.ctCourses()
      .then((res: any) => {
        setCourses(
          (res.courses ?? []).map((c: any) => ({
            label: c.course_name,
            value: String(c.id),
          })),
        );
      })
      .catch(() => setCourses([]));
  }, []);

  useEffect(() => {
    if (!isEdit || !editTestId) return;
    setLoading(true);
    LmsApi.classTest(editTestId)
      .then((res: any) => {
        const test = res.test;
        if (!test) return;
        setCourseId(String(test.course_id ?? ''));
        setSubjectId(String(test.subject_id ?? ''));
        setTestName(String(test.test_name ?? ''));
        setTestDate(String(test.test_date ?? '').slice(0, 10));
        setTotalMarks(String(test.total_marks ?? '100'));
        setPassingMarks(String(test.passing_marks ?? '40'));
      })
      .catch((e: any) => Alert.alert('Error', e?.message ?? 'Could not load test'))
      .finally(() => setLoading(false));
  }, [editTestId, isEdit]);

  useEffect(() => {
    if (!courseId) {
      setSubjects([]);
      if (!isEdit) setSubjectId('');
      return;
    }
    LmsApi.ctSubjects(Number(courseId))
      .then((res: any) => {
        setSubjects(
          (res.subjects ?? []).map((s: any) => ({
            label: s.subject_name,
            value: String(s.id),
          })),
        );
      })
      .catch(() => setSubjects([]));
  }, [courseId, isEdit]);

  function goToEnterMarks(testId?: string) {
    navigation.navigate('EnterMarks', {
      courseId: courseId || undefined,
      subjectId: subjectId || undefined,
      testId,
    });
  }

  async function submit() {
    if (!courseId || !subjectId || !testName.trim()) {
      Alert.alert('Fill all required fields');
      return;
    }
    setSaving(true);
    try {
      const body = {
        course_id: Number(courseId),
        subject_id: Number(subjectId),
        test_name: testName.trim(),
        test_date: testDate,
        total_marks: Number(totalMarks),
        passing_marks: Number(passingMarks),
      };

      if (isEdit && editTestId) {
        await LmsApi.updateClassTest(editTestId, {
          test_name: body.test_name,
          test_date: body.test_date,
          total_marks: body.total_marks,
          passing_marks: body.passing_marks,
        });
        Alert.alert('Test updated', 'Changes saved.', [
          { text: 'OK', onPress: () => navigation.goBack() },
        ]);
        return;
      }

      const res: any = await LmsApi.createClassTest(body);
      const createdTestId = res.id != null ? String(res.id) : undefined;
      Alert.alert('Test created', 'Enter marks for students now?', [
        { text: 'Later', style: 'cancel', onPress: () => navigation.navigate('WorkHub') },
        {
          text: 'Enter marks',
          onPress: () => goToEnterMarks(createdTestId),
        },
      ]);
      setTestName('');
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? (isEdit ? 'Could not update test' : 'Could not create test'));
    } finally {
      setSaving(false);
    }
  }

  if (loading) {
    return (
      <ScreenLayout title={isEdit ? 'Edit Class Test' : 'Create Class Test'} subtitle="Loading…" onBack={() => navigation.goBack()}>
        <ActivityIndicator color={PRIMARY} style={styles.loader} />
      </ScreenLayout>
    );
  }

  return (
    <ScreenLayout
      title={isEdit ? 'Edit Class Test' : 'Create Class Test'}
      subtitle={isEdit ? 'Update test details' : 'Set up a new test'}
      onBack={() => navigation.goBack()}>
      <Card>
        {!isEdit ? (
          <Text style={styles.hint}>
            After creating a test, go to Enter test marks to record student scores.
          </Text>
        ) : null}
        <FormPicker
          label="Course"
          value={courseId}
          options={courses}
          onChange={setCourseId}
          disabled={isEdit}
        />
        <FormPicker
          label="Subject"
          value={subjectId}
          options={subjects}
          onChange={setSubjectId}
          disabled={!courseId || isEdit}
        />
        <FormInput label="Test name" value={testName} onChangeText={setTestName} placeholder="Unit test 1" />
        <FormInput label="Test date (YYYY-MM-DD)" value={testDate} onChangeText={setTestDate} />
        <FormInput label="Total marks" value={totalMarks} onChangeText={setTotalMarks} keyboardType="numeric" />
        <FormInput label="Passing marks" value={passingMarks} onChangeText={setPassingMarks} keyboardType="numeric" />
        <Pressable style={[styles.btn, saving && styles.disabled]} onPress={submit} disabled={saving}>
          <Text style={styles.btnText}>
            {saving ? 'Saving…' : isEdit ? 'Save changes' : 'Create test'}
          </Text>
        </Pressable>
      </Card>

      {!isEdit ? (
        <View style={styles.nextStep}>
          <Text style={styles.nextStepLabel}>Already created a test?</Text>
          <Pressable style={styles.btnOutline} onPress={() => goToEnterMarks()}>
            <Text style={styles.btnOutlineText}>Enter test marks</Text>
          </Pressable>
        </View>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  loader: { marginTop: 24 },
  hint: {
    fontSize: 13,
    color: theme.muted,
    lineHeight: 18,
    marginBottom: 14,
  },
  btn: { backgroundColor: PRIMARY, padding: 14, borderRadius: 10, alignItems: 'center', marginTop: 8 },
  btnText: { color: '#fff', fontWeight: '700', fontSize: 16 },
  disabled: { opacity: 0.6 },
  nextStep: {
    marginTop: 8,
    padding: 16,
    backgroundColor: theme.card,
    borderRadius: 14,
  },
  nextStepLabel: {
    fontSize: 13,
    color: theme.muted,
    marginBottom: 10,
    fontWeight: '600',
  },
  btnOutline: {
    borderWidth: 1,
    borderColor: PRIMARY,
    padding: 12,
    borderRadius: 10,
    alignItems: 'center',
  },
  btnOutlineText: { color: PRIMARY, fontWeight: '700' },
});
