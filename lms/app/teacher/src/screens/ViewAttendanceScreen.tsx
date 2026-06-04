import React, { useCallback, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';

export default function ViewAttendanceScreen() {
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.attendance();
      setItems(res.records ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout title="View Attendance" subtitle="This month" refreshing={loading} onRefresh={load}>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No attendance records"
          renderItem={(r: any) => (
            <Text style={styles.line}>
              {r.date} — SID {r.sid} — {r.status} ({r.course}/{r.batch})
            </Text>
          )}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  line: { fontSize: 13, color: theme.text, marginBottom: 8 },
});
