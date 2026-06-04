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
    } catch {
      setData(null);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  const name = data?.profile?.name ?? 'Student';

  return (
    <ScreenLayout title="Dashboard" subtitle={APP_SUBTITLE} refreshing={loading} onRefresh={load}>
      <Card title={`Welcome, ${name}`}>
        <Text style={styles.muted}>Course: {data?.profile?.course_name ?? '—'}</Text>
        <Text style={styles.muted}>Batch: {data?.profile?.batch ?? '—'}</Text>
      </Card>
      <Text style={styles.section}>Quick access</Text>
      <MenuTile title="Academics" onPress={() => navigation.navigate('Academics')} />
      <MenuTile title="Assignments & tests" onPress={() => navigation.navigate('Assignments')} />
      <MenuTile title="Account" onPress={() => navigation.navigate('Account')} />
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  muted: { color: theme.muted, fontSize: 14, marginTop: 4 },
  section: { fontSize: 13, fontWeight: '700', color: theme.muted, marginBottom: 8, marginTop: 8 },
});
