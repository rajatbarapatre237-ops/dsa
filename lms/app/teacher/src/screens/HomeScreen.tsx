import React, { useCallback, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card, MenuTile } from '../components/Card';
import { LmsApi } from '../api/lms';
import { APP_SUBTITLE } from '../config';
import { theme } from '../ui/theme';

export default function HomeScreen() {
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

  const t = data?.teacher;

  return (
    <ScreenLayout title="Dashboard" subtitle={APP_SUBTITLE} refreshing={loading} onRefresh={load}>
      <Card title={`Welcome, ${t?.name ?? t?.email ?? 'Teacher'}`}>
        <Text style={styles.muted}>Students: {data?.student_count ?? 0}</Text>
        <Text style={styles.muted}>Courses assigned: {data?.assigned_courses?.length ?? 0}</Text>
      </Card>
      <MenuTile title="View Students" onPress={() => navigation.navigate('Students')} />
      <MenuTile title="Attendance" onPress={() => navigation.navigate('Attendance')} />
      <MenuTile title="Work (assignments & tests)" onPress={() => navigation.navigate('Work')} />
      <MenuTile title="Account" onPress={() => navigation.navigate('Account')} />
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  muted: { color: theme.muted, fontSize: 14, marginTop: 4 },
});
