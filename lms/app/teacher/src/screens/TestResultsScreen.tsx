import React, { useCallback, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { LmsApi } from '../api/lms';
import { WorkStackParamList } from '../navigation/types';

export default function TestResultsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.testResults();
      setItems(res.results ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="Class Test Results"
      subtitle="Tap for details"
      onBack={() => navigation.navigate('WorkHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No test results yet"
          renderItem={(r: any) => (
            <ListRow
              title={r.test_name}
              subtitle={`${r.test_date} · ${r.marks_obtained}/${r.total_marks}`}
              onPress={() => navigation.navigate('ClassTestResultDetail', { result: r })}
            />
          )}
        />
      </Card>
    </ScreenLayout>
  );
}
