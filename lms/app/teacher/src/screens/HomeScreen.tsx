import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View, Pressable } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { ActionCard, Card } from '../components/Card';
import { HeroCard, SectionTitle, StatGrid } from '../components/DashboardUi';
import { LmsApi } from '../api/lms';
import { APP_SUBTITLE, PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { settleApiCalls } from '../utils/apiError';

type Summary = {
  dashboard: any;
  assignments: any[];
  attendanceCount: number;
  tests: any[];
};

function isActiveAssignment(item: any) {
  return item.status === 1 || item.status === '1' || item.status === true;
}

function isMeaningfulActiveAssignment(item: any) {
  if (!isActiveAssignment(item)) return false;
  const name = String(item.document_name ?? '').trim();
  const batch = String(item.batch_name ?? '').trim();
  return !!(name || batch);
}

function assignmentTitle(item: any) {
  const name = String(item.document_name ?? '').trim();
  if (name) return name;
  const batch = String(item.batch_name ?? '').trim();
  if (batch) return `Assignment for ${batch}`;
  return 'Untitled assignment';
}

function assignmentSubtitle(item: any) {
  const batch = String(item.batch_name ?? '').trim();
  if (batch) return batch;
  return item.type === 'link' ? 'Link assignment' : 'File assignment';
}

export default function HomeScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [summary, setSummary] = useState<Summary | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const [dashRes, assignRes, attendRes, testsRes] = await settleApiCalls([
        LmsApi.dashboard(),
        LmsApi.assignments(),
        LmsApi.attendance(),
        LmsApi.classTests(),
      ]);
      setSummary({
        dashboard: (dashRes as any)?.dashboard ?? dashRes ?? null,
        assignments: (assignRes as any)?.assignments ?? [],
        attendanceCount: Number((attendRes as any)?.today_present_students ?? 0),
        tests: (testsRes as any)?.tests ?? [],
      });
    } catch {
      setSummary(null);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  const data = summary?.dashboard;
  const teacher = data?.teacher;
  const courses: string[] = data?.assigned_courses ?? [];
  const activeAssignments = (summary?.assignments ?? []).filter(isMeaningfulActiveAssignment);
  const recentTests = (summary?.tests ?? []).slice(0, 3);
  const recentAssignments = activeAssignments.slice(0, 3);
  const teacherName = teacher?.name ?? teacher?.email ?? 'Teacher';

  return (
    <ScreenLayout title="Dashboard" refreshing={loading} onRefresh={load}>
      <HeroCard
        eyebrow={APP_SUBTITLE}
        title={`Welcome, ${teacherName}`}
        subtitle={teacher?.email ?? 'Manage classes, attendance, and assessments'}
        avatarLabel={teacherName}
        chips={
          teacher?.course
            ? [{ label: teacher.course, icon: 'school-outline' }]
            : [{ label: `${courses.length} courses`, icon: 'library-outline' }]
        }
      />

      <SectionTitle>Overview</SectionTitle>
      <StatGrid
        items={[
          { label: 'Students', value: data?.student_count ?? 0, tint: theme.primarySoft },
          { label: 'Active assignments', value: activeAssignments.length, tint: '#e0f2fe' },
          { label: "Today's attendance", value: summary?.attendanceCount ?? 0, tint: '#dcfce7' },
          { label: 'Class tests', value: summary?.tests?.length ?? 0, tint: '#ede9fe' },
        ]}
      />

      {courses.length > 0 ? (
        <>
          <SectionTitle>Your courses</SectionTitle>
          <Card>
            {courses.map((course, index) => (
              <View key={course} style={[styles.courseRow, index > 0 && styles.listItemBorder]}>
                <View style={styles.courseDot} />
                <Text style={styles.listItem}>{course}</Text>
              </View>
            ))}
          </Card>
        </>
      ) : null}

      <SectionTitle>Quick actions</SectionTitle>
      <ActionCard
        iconName="calendar-check"
        title="Add attendance"
        subtitle="Mark students for today"
        accent="#dcfce7"
        onPress={() => navigation.navigate('Attendance', { screen: 'AddAttendance' })}
      />
      <ActionCard
        iconName="file-document-outline"
        title="Add assignment"
        subtitle="Share a link or file with a batch"
        accent="#e0f2fe"
        onPress={() => navigation.navigate('Work', { screen: 'AddAssignment' })}
      />
      <ActionCard
        iconName="clipboard-text-outline"
        title="Create class test"
        subtitle="Set up a new test"
        accent="#ede9fe"
        onPress={() => navigation.navigate('Work', { screen: 'CreateClassTest' })}
      />

      {recentTests.length > 0 ? (
        <>
          <SectionTitle>Recent class tests</SectionTitle>
          <Card>
            {recentTests.map((test: any, index: number) => (
              <Pressable
                key={test.id}
                style={[styles.recentRow, index > 0 && styles.listItemBorder]}
                onPress={() => navigation.navigate('Work', { screen: 'ClassTests' })}>
                <Text style={styles.recentTitle}>{test.test_name}</Text>
                <Text style={styles.muted}>
                  {test.course_name} · {test.test_date}
                </Text>
              </Pressable>
            ))}
          </Card>
        </>
      ) : null}

      <SectionTitle>Active assignments</SectionTitle>
      <Card>
        {recentAssignments.length > 0 ? (
          recentAssignments.map((assignment: any, index: number) => (
            <Pressable
              key={assignment.id}
              style={[styles.recentRow, index > 0 && styles.listItemBorder]}
              onPress={() => navigation.navigate('Work', { screen: 'AssignmentsList' })}>
              <Text style={styles.recentTitle}>{assignmentTitle(assignment)}</Text>
              <Text style={styles.muted}>{assignmentSubtitle(assignment)}</Text>
            </Pressable>
          ))
        ) : (
          <Text style={styles.empty}>No active assignments</Text>
        )}
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  muted: { color: theme.muted, fontSize: 14, marginTop: 4 },
  courseRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 6,
  },
  courseDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: PRIMARY,
    marginRight: 10,
  },
  listItem: {
    flex: 1,
    fontSize: 14,
    color: theme.text,
    fontWeight: '600',
  },
  listItemBorder: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
    marginTop: 4,
    paddingTop: 10,
  },
  recentRow: {
    paddingVertical: 8,
  },
  recentTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: theme.text,
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 8,
  },
});
