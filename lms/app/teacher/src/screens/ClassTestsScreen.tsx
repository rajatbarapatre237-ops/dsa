import React, { useCallback, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';

export default function ClassTestsScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.classTests();
      setItems(res.tests ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="Class Tests"
      subtitle="Create & marks (view)"
      onBack={() => navigation.navigate('WorkHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <Text style={styles.hint}>Use Create Class Test or Enter Test Marks from Home / Work tab.</Text>
        <DataList
          loading={loading}
          items={items}
          emptyText="No class tests"
          renderItem={(t: any) => (
            <Text style={styles.line}>
              {t.test_name} — {t.course_name} — {t.test_date}
            </Text>
          )}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  hint: { color: theme.muted, marginBottom: 12, fontSize: 13 },
  line: { fontSize: 14, color: theme.text, marginBottom: 8 },
});
