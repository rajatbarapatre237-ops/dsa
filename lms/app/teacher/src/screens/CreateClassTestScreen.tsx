import React, { useEffect, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
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
    if (!courseId) {
      setSubjects([]);
      setSubjectId('');
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
  }, [courseId]);

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
      const res: any = await LmsApi.createClassTest({
        course_id: Number(courseId),
        subject_id: Number(subjectId),
        test_name: testName.trim(),
        test_date: testDate,
        total_marks: Number(totalMarks),
        passing_marks: Number(passingMarks),
      });
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
      Alert.alert('Error', e?.message ?? 'Could not create test');
    } finally {
      setSaving(false);
    }
  }

  return (
    <ScreenLayout
      title="Create Class Test"
      subtitle="Set up a new test"
      onBack={() => navigation.navigate('WorkHub')}>
      <Card>
        <Text style={styles.hint}>
          After creating a test, go to Enter test marks to record student scores.
        </Text>
        <FormPicker label="Course" value={courseId} options={courses} onChange={setCourseId} />
        <FormPicker
          label="Subject"
          value={subjectId}
          options={subjects}
          onChange={setSubjectId}
          disabled={!courseId}
        />
        <FormInput label="Test name" value={testName} onChangeText={setTestName} placeholder="Unit test 1" />
        <FormInput label="Test date (YYYY-MM-DD)" value={testDate} onChangeText={setTestDate} />
        <FormInput label="Total marks" value={totalMarks} onChangeText={setTotalMarks} keyboardType="numeric" />
        <FormInput label="Passing marks" value={passingMarks} onChangeText={setPassingMarks} keyboardType="numeric" />
        <Pressable style={[styles.btn, saving && styles.disabled]} onPress={submit} disabled={saving}>
          <Text style={styles.btnText}>{saving ? 'Creating…' : 'Create test'}</Text>
        </Pressable>
      </Card>

      <View style={styles.nextStep}>
        <Text style={styles.nextStepLabel}>Already created a test?</Text>
        <Pressable style={styles.btnOutline} onPress={() => goToEnterMarks()}>
          <Text style={styles.btnOutlineText}>Enter test marks</Text>
        </Pressable>
      </View>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
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
