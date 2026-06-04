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

  const child = data?.child;
  const att = data?.today_attendance;

  return (
    <ScreenLayout title="Dashboard" subtitle={APP_SUBTITLE} refreshing={loading} onRefresh={load}>
      <Card title={`Parent of ${child?.name ?? 'Student'}`}>
        <Text style={styles.line}>Student ID: {child?.id ?? '—'}</Text>
        <Text style={styles.line}>Course: {child?.course_name ?? '—'}</Text>
      </Card>
      <Card title="Today's attendance">
        <Text style={styles.line}>Date: {att?.date}</Text>
        <Text style={styles.line}>Status: {att?.status || '—'}</Text>
        <Text style={styles.line}>Entry: {att?.entry_time}</Text>
        <Text style={styles.line}>Exit: {att?.exit_time}</Text>
      </Card>
      <MenuTile title="Attendance" onPress={() => navigation.navigate('Attendance')} />
      <MenuTile title="Marks & results" onPress={() => navigation.navigate('Marks')} />
      <MenuTile title="Account" onPress={() => navigation.navigate('Account')} />
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  line: { fontSize: 14, color: theme.text, marginTop: 4 },
});
