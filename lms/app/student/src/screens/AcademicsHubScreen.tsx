import React, { useMemo } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import {
  MonthAttendanceSummary,
  RecentAttendanceCard,
  SectionHeader,
  SectionTitle,
  StudentContextCard,
} from '../components/DashboardUi';
import {
  ExploreAcademicsTiles,
  attendanceFromSummary,
  monthAttendanceFromRecords,
} from '../components/StudentHubUi';
import {
  ExploreMarksTiles,
  MarksOverviewCard,
  RecentMarksCard,
  RecentMarksCarousel,
  TestResult,
  marksStats,
} from '../components/MarksUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { useRefreshStudentOnFocus } from '../hooks/useRefreshStudentOnFocus';
import { AcademicsStackParamList } from '../navigation/types';

function sortByDateDesc(results: TestResult[]) {
  return [...results].sort((a, b) => String(b.test_date ?? '').localeCompare(String(a.test_date ?? '')));
}

export default function AcademicsHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AcademicsStackParamList>>();
  const ctx = useStudentContext();
  useRefreshStudentOnFocus();

  const monthLabel = useMemo(
    () => new Date().toLocaleString('en-IN', { month: 'long', year: 'numeric' }),
    [],
  );

  const attendance = useMemo(() => {
    if (ctx.monthRecords.length > 0) {
      return monthAttendanceFromRecords(ctx.monthRecords);
    }
    return attendanceFromSummary(ctx.attendanceSummary);
  }, [ctx.monthRecords, ctx.attendanceSummary]);

  const recentRecords = [...ctx.monthRecords]
    .sort((a, b) => String(b.date).localeCompare(String(a.date)))
    .slice(0, 5);

  const sortedMarks = useMemo(() => sortByDateDesc(ctx.marksResults), [ctx.marksResults]);
  const marksOverview = marksStats(sortedMarks);
  const recentMarks = sortedMarks.slice(0, 5);
  const carouselMarks = sortedMarks.slice(0, 8);

  const openMarkDetail = (result: TestResult) => {
    navigation.navigate('ClassTestResultDetail', { result: result as Record<string, unknown> });
  };

  return (
    <ScreenLayout
      title="Academics"
      refreshing={ctx.refreshing}
      onRefresh={() => ctx.refresh({ showRefresh: true })}>
      <StudentContextCard
        name={ctx.name}
        studentId={ctx.studentId}
        course={ctx.course}
        batch={ctx.batch}
        profile={ctx.profile}
        attendanceSummary={ctx.attendanceSummary}
        monthRecords={ctx.monthRecords}
      />

      <SectionTitle>Attendance</SectionTitle>
      <MonthAttendanceSummary
        monthLabel={monthLabel}
        present={attendance.present}
        absent={attendance.absent}
        total={attendance.total}
        percent={attendance.percent}
      />

      <SectionTitle>Marks</SectionTitle>
      <RecentMarksCarousel results={carouselMarks} onItemPress={openMarkDetail} />
      <MarksOverviewCard
        total={marksOverview.total}
        passed={marksOverview.passed}
        failed={marksOverview.failed}
        averagePercent={marksOverview.averagePercent}
        passRate={marksOverview.passRate}
      />
      <ExploreMarksTiles
        onAllMarks={() => navigation.navigate('AllTestMarks')}
        onClassResults={() => navigation.navigate('TestResults')}
      />

      <SectionTitle>Explore</SectionTitle>
      <ExploreAcademicsTiles
        onCourses={() => navigation.navigate('Courses')}
        onAttendance={() => navigation.navigate('Attendance')}
        onTransactions={() => navigation.navigate('Transactions')}
      />

      <SectionHeader
        title="Recent days"
        actionLabel="See all"
        onAction={() => navigation.navigate('Attendance')}
      />
      <RecentAttendanceCard records={recentRecords} />

      <SectionHeader
        title="Recent tests"
        actionLabel="See all"
        onAction={() => navigation.navigate('AllTestMarks')}
      />
      <RecentMarksCard results={recentMarks} onItemPress={openMarkDetail} showHeader={false} />
    </ScreenLayout>
  );
}
