import React, { useCallback, useMemo, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import {
  AttendanceOverviewCard,
  ExploreAttendanceTiles,
  MonthAttendanceSummary,
  RecentAttendanceCard,
  SectionTitle,
  StudentContextCard,
  monthAttendanceStats,
} from '../components/DashboardUi';
import { LmsApi } from '../api/lms';
import { AttendanceStackParamList } from '../navigation/types';

export default function AttendanceHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AttendanceStackParamList>>();
  const [loading, setLoading] = useState(true);
  const [dashboard, setDashboard] = useState<any>(null);
  const [monthRecords, setMonthRecords] = useState<any[]>([]);

  const month = useMemo(() => new Date().toISOString().slice(0, 7), []);
  const monthLabel = useMemo(
    () =>
      new Date().toLocaleString('en-IN', {
        month: 'long',
        year: 'numeric',
      }),
    [],
  );

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const [dashRes, attendRes]: any[] = await Promise.all([
        LmsApi.dashboard(),
        LmsApi.attendance(month),
      ]);
      setDashboard(dashRes.dashboard ?? dashRes);
      setMonthRecords(attendRes.records ?? []);
    } finally {
      setLoading(false);
    }
  }, [month]);

  React.useEffect(() => {
    load();
  }, [load]);

  const child = dashboard?.child;
  const att = dashboard?.today_attendance;
  const stats = monthAttendanceStats(monthRecords);
  const recentRecords = [...monthRecords]
    .sort((a, b) => String(b.date).localeCompare(String(a.date)))
    .slice(0, 5);

  return (
    <ScreenLayout title="Attendance" refreshing={loading} onRefresh={load}>
      <StudentContextCard name={child?.name} course={child?.course_name} batch={child?.batch} />

      <AttendanceOverviewCard
        date={att?.date}
        status={att?.status}
        entry={att?.entry_time}
        exit={att?.exit_time}
        onPress={() => navigation.navigate('TodayAttendance')}
      />

      <MonthAttendanceSummary
        monthLabel={monthLabel}
        present={stats.present}
        absent={stats.absent}
        total={stats.total}
        percent={stats.percent}
      />

      <SectionTitle>Explore</SectionTitle>
      <ExploreAttendanceTiles
        onToday={() => navigation.navigate('TodayAttendance')}
        onMonthly={() => navigation.navigate('MonthlyAttendance')}
      />

      <RecentAttendanceCard
        records={recentRecords}
        onViewAll={() => navigation.navigate('MonthlyAttendance')}
      />
    </ScreenLayout>
  );
}
