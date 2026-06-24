import React, { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { FormPicker } from '../components/FormPicker';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { AttendanceStackParamList } from '../navigation/types';
import { theme } from '../ui/theme';
import { formatStudentDisplayId, formatStudentSubtitle } from '../utils/student';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

type StudentRow = {
  id: string | number;
  name?: string;
  course_name?: string;
  batch?: string;
  session_name?: string;
};

export default function StudentAttendanceListScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AttendanceStackParamList>>();
  const [courseOptions, setCourseOptions] = useState<{ label: string; value: string }[]>([]);
  const [course, setCourse] = useState('');
  const [loadingCourses, setLoadingCourses] = useState(true);
  const [loadingStudents, setLoadingStudents] = useState(false);
  const [loadedCourse, setLoadedCourse] = useState('');
  const [students, setStudents] = useState<StudentRow[]>([]);

  useEffect(() => {
    LmsApi.formCourses()
      .then((res: any) => {
        const courses: string[] = res.courses ?? [];
        setCourseOptions(courses.map(name => ({ label: name, value: name })));
      })
      .catch(() => setCourseOptions([]))
      .finally(() => setLoadingCourses(false));
  }, []);

  const loadStudents = useCallback(async () => {
    if (!course) return;

    setLoadingStudents(true);
    try {
      const res: any = await LmsApi.students({ course, per_page: 100 });
      setStudents(res.students ?? []);
      setLoadedCourse(course);
    } catch {
      setStudents([]);
      setLoadedCourse(course);
    } finally {
      setLoadingStudents(false);
    }
  }, [course]);

  const refreshStudents = useCallback(() => {
    if (loadedCourse) {
      loadStudents();
    }
  }, [loadedCourse, loadStudents]);

  useRefreshOnFocus(refreshStudents);

  return (
    <ScreenLayout
      title="Student summaries"
      subtitle="Students by course"
      onBack={() => navigation.goBack()}>
      <Card title="Select course">
        {loadingCourses ? (
          <ActivityIndicator color={PRIMARY} />
        ) : courseOptions.length > 0 ? (
          <>
            <FormPicker
              label="Course"
              value={course}
              options={courseOptions}
              onChange={value => {
                setCourse(value);
                setLoadedCourse('');
                setStudents([]);
              }}
              placeholder="Select course"
            />
            <Pressable
              style={[styles.loadBtn, (!course || loadingStudents) && styles.loadBtnDisabled]}
              onPress={loadStudents}
              disabled={!course || loadingStudents}>
              {loadingStudents ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.loadBtnText}>Load students</Text>
              )}
            </Pressable>
          </>
        ) : (
          <Text style={styles.empty}>No assigned courses found.</Text>
        )}
      </Card>

      {loadedCourse ? (
        <Card title={`Students — ${loadedCourse}`}>
          <DataList
            loading={loadingStudents}
            items={students}
            emptyText="No students found for this course."
            renderItem={(student: StudentRow) => (
              <ListRow
                title={student.name ?? 'Student'}
                subtitle={[
                  formatStudentDisplayId(student.id),
                  formatStudentSubtitle(student),
                  student.session_name ? `Session ${student.session_name}` : null,
                ]
                  .filter(Boolean)
                  .join(' · ')}
                onPress={() =>
                  navigation.navigate('StudentAttendanceSummary', {
                    id: String(student.id),
                    name: student.name,
                  })
                }
              />
            )}
          />
        </Card>
      ) : course ? (
        <Text style={styles.hint}>Select a course and tap Load students.</Text>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  loadBtn: {
    backgroundColor: PRIMARY,
    borderRadius: 10,
    paddingVertical: 12,
    alignItems: 'center',
    marginTop: 2,
  },
  loadBtnDisabled: {
    opacity: 0.6,
  },
  loadBtnText: {
    color: '#fff',
    fontSize: 15,
    fontWeight: '700',
  },
  hint: {
    fontSize: 13,
    color: theme.muted,
    textAlign: 'center',
    marginTop: 4,
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 8,
  },
});
