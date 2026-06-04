import React, { useCallback, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';

export default function TransactionsScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.transactions();
      setItems(res.transactions ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="Transactions"
      subtitle="Fee payments"
      onBack={() => navigation.navigate('AcademicsHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No transactions"
          renderItem={(t: any) => (
            <Text style={styles.line}>
              {t.date ?? t.id}: ₹{t.amount ?? t.paid ?? '—'} — {t.mode ?? ''}
            </Text>
          )}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  line: { fontSize: 14, color: theme.text, marginBottom: 10, paddingBottom: 8, borderBottomWidth: 1, borderBottomColor: '#eee' },
});
