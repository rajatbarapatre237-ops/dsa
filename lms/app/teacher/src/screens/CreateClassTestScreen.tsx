import React, { useEffect, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert } from 'react-native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import { FormInput } from '../components/FormInput';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';

export default function CreateClassTestScreen() {
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
    LmsApi.ctCourses().then((res: any) => {
      setCourses(
        (res.courses ?? []).map((c: any) => ({
          label: c.course_name,
          value: String(c.id),
        })),
      );
    });
  }, []);

  useEffect(() => {
    if (!courseId) {
      setSubjects([]);
      setSubjectId('');
      return;
    }
    LmsApi.ctSubjects(Number(courseId)).then((res: any) => {
      setSubjects(
        (res.subjects ?? []).map((s: any) => ({
          label: s.subject_name,
          value: String(s.id),
        })),
      );
    });
  }, [courseId]);

  async function submit() {
    if (!courseId || !subjectId || !testName.trim()) {
      Alert.alert('Fill all required fields');
      return;
    }
    setSaving(true);
    try {
      await LmsApi.createClassTest({
        course_id: Number(courseId),
        subject_id: Number(subjectId),
        test_name: testName.trim(),
        test_date: testDate,
        total_marks: Number(totalMarks),
        passing_marks: Number(passingMarks),
      });
      Alert.alert('Success', 'Class test created');
      setTestName('');
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not create test');
    } finally {
      setSaving(false);
    }
  }

  return (
    <ScreenLayout title="Create Class Test" subtitle="New test for your subject">
      <Card>
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
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  btn: { backgroundColor: PRIMARY, padding: 14, borderRadius: 10, alignItems: 'center', marginTop: 8 },
  btnText: { color: '#fff', fontWeight: '700', fontSize: 16 },
  disabled: { opacity: 0.6 },
});
