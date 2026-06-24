import React, { useCallback, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { SectionTitle, StudentContextCard } from '../components/DashboardUi';
import { AssignmentDetailRow } from '../components/AssignmentUi';
import { CourseHeroCard } from '../components/AcademicsUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { LmsApi } from '../api/lms';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';

export default function CoursesScreen() {
  const navigation = useNavigation<any>();
  const ctx = useStudentContext();
  const { refresh: refreshContext, refreshing: ctxRefreshing } = ctx;
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [data, setData] = useState<any>(null);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const result = await Promise.allSettled([LmsApi.courses()]);
        if (result[0].status === 'fulfilled') {
          setData(result[0].value);
        } else {
          setData(null);
        }
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  const refresh = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      await Promise.all([refreshContext(options), load(options)]);
    },
    [refreshContext, load],
  );

  useRefreshOnFocus(refresh);

  const c = data?.current_course;
  const p = data?.profile ?? ctx.profile;
  const courseName = c?.course_name ?? p?.course_name ?? ctx.course;
  const batch = p?.batch ?? ctx.batch;
  const fees = c?.course_fees ?? p?.course_fees;
  const balance = p?.balance_fees;

  return (
    <ScreenLayout
      title="Courses"
      subtitle="Your enrollment"
      onBack={() => navigation.navigate('AcademicsHub')}
      refreshing={refreshing || ctxRefreshing}
      onRefresh={() => refresh({ showRefresh: true })}>
      <StudentContextCard
        name={ctx.name}
        studentId={ctx.studentId}
        course={ctx.course}
        batch={ctx.batch}
        profile={ctx.profile}
        attendanceSummary={ctx.attendanceSummary}
        monthRecords={ctx.monthRecords}
      />

      <CourseHeroCard
        courseName={courseName}
        batch={batch}
        fees={fees}
        balance={balance}
      />

      <SectionTitle>Enrollment details</SectionTitle>
      <Card>
        <AssignmentDetailRow label="Course" value={courseName} />
        <AssignmentDetailRow label="Batch" value={batch} />
        <AssignmentDetailRow label="Course fees" value={fees != null ? `₹${Number(fees).toLocaleString('en-IN')}` : null} />
        <AssignmentDetailRow label="Balance fees" value={balance != null ? `₹${Number(balance).toLocaleString('en-IN')}` : null} />
      </Card>
    </ScreenLayout>
  );
}
