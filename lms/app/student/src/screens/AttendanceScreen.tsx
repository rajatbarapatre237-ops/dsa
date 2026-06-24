import React, { useCallback, useMemo, useState } from 'react';
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
  attendanceFromSummary,
  monthAttendanceFromRecords,
  recordsForMonth,
} from '../components/StudentHubUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { LmsApi } from '../api/lms';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';

export default function AttendanceScreen() {
  const navigation = useNavigation<any>();
  const ctx = useStudentContext();
  const { refresh: refreshContext, refreshing: ctxRefreshing } = ctx;
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [records, setRecords] = useState<any[]>([]);
  const [summary, setSummary] = useState<any[]>([]);

  const month = useMemo(() => new Date().toISOString().slice(0, 7), []);
  const monthLabel = useMemo(
    () => new Date().toLocaleString('en-IN', { month: 'long', year: 'numeric' }),
    [],
  );

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const result = await Promise.allSettled([LmsApi.attendance(month)]);
        if (result[0].status === 'fulfilled') {
          const data = result[0].value;
          setSummary(data.summary ?? []);
          setRecords(data.records ?? []);
        } else {
          setSummary([]);
          setRecords([]);
        }
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData, month],
  );

  const refresh = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      await Promise.all([refreshContext(options), load(options)]);
    },
    [refreshContext, load],
  );

  useRefreshOnFocus(refresh);

  const monthRecords = useMemo(() => recordsForMonth(records, month), [records, month]);
  const attendance = useMemo(() => {
    if (monthRecords.length > 0) {
      return monthAttendanceFromRecords(monthRecords);
    }
    return attendanceFromSummary(summary.length ? summary : ctx.attendanceSummary);
  }, [monthRecords, summary, ctx.attendanceSummary]);

  const recentRecords = [...records]
    .sort((a, b) => String(b.date).localeCompare(String(a.date)))
    .slice(0, 10);

  return (
    <ScreenLayout
      title="Attendance"
      subtitle="Monthly history"
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

      <SectionTitle>Attendance</SectionTitle>
      <MonthAttendanceSummary
        monthLabel={monthLabel}
        present={attendance.present}
        absent={attendance.absent}
        total={attendance.total}
        percent={attendance.percent}
      />

      <SectionHeader title="All records" />
      <RecentAttendanceCard records={recentRecords} />
    </ScreenLayout>
  );
}
