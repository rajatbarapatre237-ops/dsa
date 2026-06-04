import React, { useCallback, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { LmsApi } from '../api/lms';
import { StudentsStackParamList } from '../navigation/types';

export default function StudentsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<StudentsStackParamList>>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.students();
      setItems(res.students ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout title="Students" subtitle="Tap for profile" refreshing={loading} onRefresh={load}>
      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText="No students found"
          renderItem={(s: any) => (
            <ListRow
              title={s.name}
              subtitle={`${s.course_name} / ${s.batch}`}
              right={`ID ${s.id}`}
              onPress={() => navigation.navigate('StudentDetail', { id: String(s.id) })}
            />
          )}
        />
      </Card>
    </ScreenLayout>
  );
}
