import React, { useCallback, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';

export default function SalaryScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.salary();
      setItems(res.salary ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="View Salary"
      subtitle="Account"
      onBack={() => navigation.navigate('AccountHome')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No salary records"
          renderItem={(s: any) => (
            <Text style={styles.line}>
              {s.month ?? s.date ?? s.id}: ₹{s.amount ?? s.salary ?? '—'}
            </Text>
          )}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  line: { fontSize: 14, color: theme.text, marginBottom: 10 },
});
