import React, { useCallback, useMemo, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import {
  MonthAttendanceSummary,
  RecentAttendanceCard,
  monthAttendanceStats,
} from '../components/DashboardUi';
import { LmsApi } from '../api/lms';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

export default function MonthlyAttendanceScreen() {
  const navigation = useNavigation<any>();
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<any[]>([]);
  const month = useMemo(() => new Date().toISOString().slice(0, 7), []);
  const monthLabel = useMemo(
    () =>
      new Date().toLocaleString('en-IN', {
        month: 'long',
        year: 'numeric',
      }),
    [],
  );

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.attendance(month);
        setItems(res.records ?? []);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData, month],
  );

  useRefreshOnFocus(load);

  const stats = monthAttendanceStats(items);
  const sortedRecords = [...items].sort((a, b) => String(b.date).localeCompare(String(a.date)));

  return (
    <ScreenLayout
      title="Monthly Attendance"
      subtitle={monthLabel}
      onBack={() => navigation.navigate('AttendanceHub')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <MonthAttendanceSummary
        monthLabel={monthLabel}
        present={stats.present}
        absent={stats.absent}
        total={stats.total}
        percent={stats.percent}
      />

      <RecentAttendanceCard records={sortedRecords} />
    </ScreenLayout>
  );
}
