import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { AttendanceOverviewCard } from '../components/DashboardUi';
import { LmsApi } from '../api/lms';
import { formatStudentDisplayId } from '../utils/studentId';
import { theme } from '../ui/theme';

export default function TodayAttendanceScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<any>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.dashboard();
      setData(res.dashboard ?? res);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  const att = data?.today_attendance;
  const child = data?.child;

  return (
    <ScreenLayout
      title="Today's attendance"
      subtitle={child?.name ?? 'Your child'}
      onBack={() => navigation.navigate('AttendanceHub')}
      refreshing={loading}
      onRefresh={load}>
      <AttendanceOverviewCard
        date={att?.date}
        status={att?.status}
        entry={att?.entry_time}
        exit={att?.exit_time}
      />

      <Card title="Student details">
        <View style={styles.detailRow}>
          <Text style={styles.label}>Course</Text>
          <Text style={styles.value}>{child?.course_name ?? '—'}</Text>
        </View>
        <View style={styles.detailRow}>
          <Text style={styles.label}>Batch</Text>
          <Text style={styles.value}>{child?.batch ?? '—'}</Text>
        </View>
        <View style={styles.detailRow}>
          <Text style={styles.label}>Student ID</Text>
          <Text style={styles.value}>{formatStudentDisplayId(child?.id) ?? '—'}</Text>
        </View>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  label: { fontSize: 13, fontWeight: '600', color: theme.muted },
  value: { fontSize: 14, fontWeight: '700', color: theme.text },
});
