import React, { useCallback, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';

export default function AttendanceScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<any>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      setData(await LmsApi.attendance());
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  const summary = data?.summary ?? [];
  const records = data?.records ?? [];

  return (
    <ScreenLayout
      title="Attendance"
      subtitle="Academics"
      onBack={() => navigation.navigate('AcademicsHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card title="Summary">
        <DataList
          loading={false}
          items={summary}
          emptyText="No attendance summary"
          renderItem={(a: any) => (
            <Text style={styles.line}>
              {a.course}: {a.present_days}/{a.total_days} present
            </Text>
          )}
        />
      </Card>
      <Card title="Recent records">
        <DataList
          loading={loading}
          items={records}
          emptyText="No records"
          renderItem={(r: any) => (
            <Text style={styles.line}>
              {r.date} — {r.status} ({r.course})
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
