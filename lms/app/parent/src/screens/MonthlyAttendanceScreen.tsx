import React, { useCallback, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';

export default function MonthlyAttendanceScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);
  const month = new Date().toISOString().slice(0, 7);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.attendance(month);
      setItems(res.records ?? []);
    } finally {
      setLoading(false);
    }
  }, [month]);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="Monthly Attendance"
      subtitle={month}
      onBack={() => navigation.navigate('AttendanceHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No attendance this month"
          renderItem={(r: any) => (
            <Text style={styles.line}>
              {r.date}: {r.status} — in {r.entry_time ?? '—'} / out {r.exit_time ?? '—'}
            </Text>
          )}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  line: { fontSize: 14, color: theme.text, marginBottom: 8 },
});
