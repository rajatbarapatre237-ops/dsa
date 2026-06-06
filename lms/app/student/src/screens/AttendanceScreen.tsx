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

export default function AttendanceScreen() {
  const navigation = useNavigation<any>();
  const ctx = useStudentContext();
  const [loading, setLoading] = useState(true);
  const [records, setRecords] = useState<any[]>([]);
  const [summary, setSummary] = useState<any[]>([]);

  const month = useMemo(() => new Date().toISOString().slice(0, 7), []);
  const monthLabel = useMemo(
    () => new Date().toLocaleString('en-IN', { month: 'long', year: 'numeric' }),
    [],
  );

  const load = useCallback(async () => {
    setLoading(true);
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
    } finally {
      setLoading(false);
    }
  }, [month]);

  React.useEffect(() => {
    load();
  }, [load]);

  const refresh = useCallback(async () => {
    await Promise.all([ctx.refresh(), load()]);
  }, [ctx, load]);

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
      refreshing={loading || ctx.loading}
      onRefresh={refresh}>
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
