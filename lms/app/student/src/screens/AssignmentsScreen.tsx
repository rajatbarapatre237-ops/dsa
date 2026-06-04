import React, { useCallback, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { LmsApi } from '../api/lms';
import { AssignmentsStackParamList } from '../navigation/types';

export default function AssignmentsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.assignments();
      setItems(res.assignments ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="Questions"
      subtitle="Tap for details"
      onBack={() => navigation.navigate('AssignmentsHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No assignments for your batch"
          renderItem={(a: any) => (
            <ListRow
              title={a.document_name}
              subtitle={`${a.type} · ${a.batch_name}`}
              onPress={() => navigation.navigate('AssignmentDetail', { id: a.id })}
            />
          )}
        />
      </Card>
    </ScreenLayout>
  );
}
