import React, { useMemo } from 'react';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import {
  MonthAttendanceSummary,
  RecentAttendanceCard,
  SectionHeader,
  SectionTitle,
  StudentContextCard,
} from '../components/DashboardUi';
import {
  ExploreQuickTiles,
  RecentAssignmentsCard,
  attendanceFromSummary,
  monthAttendanceFromRecords,
} from '../components/StudentHubUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { useRefreshStudentOnFocus } from '../hooks/useRefreshStudentOnFocus';

export default function HomeScreen() {
  const navigation = useNavigation<any>();
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

  const recentAssignments = ctx.assignments.slice(0, 4);
  const recentAttendance = [...ctx.monthRecords]
    .sort((a, b) => String(b.date).localeCompare(String(a.date)))
    .slice(0, 3);

  return (
    <ScreenLayout title="Dashboard" refreshing={ctx.loading} onRefresh={ctx.refresh}>
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

      <SectionHeader
        title="Assignments"
        actionLabel="See all"
        onAction={() => navigation.navigate('Assignments')}
      />
      <RecentAssignmentsCard
        assignments={recentAssignments}
        onItemPress={item => {
          if (item?.id != null) {
            navigation.navigate('Assignments', {
              screen: 'AssignmentDetail',
              params: { id: Number(item.id) },
            });
          } else {
            navigation.navigate('Assignments');
          }
        }}
      />

      {recentAttendance.length > 0 ? (
        <>
          <SectionHeader
            title="Recent days"
            actionLabel="See all"
            onAction={() => navigation.navigate('Academics')}
          />
          <RecentAttendanceCard records={recentAttendance} />
        </>
      ) : null}

      <SectionTitle>Quick access</SectionTitle>
      <ExploreQuickTiles
        onAcademics={() => navigation.navigate('Academics')}
        onAssignments={() => navigation.navigate('Assignments')}
        onAccount={() => navigation.navigate('Account')}
      />
    </ScreenLayout>
  );
}
