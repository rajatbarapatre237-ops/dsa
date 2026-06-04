import React, { useCallback, useState } from 'react';
import { Alert, ActivityIndicator } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import DetailRow from '../components/DetailRow';
import ListRow from '../components/ListRow';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { StudentsStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<StudentsStackParamList, 'StudentDetail'>;

export default function StudentDetailScreen({ navigation, route }: Props) {
  const { id } = route.params;
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<any>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      setData(await LmsApi.student(id));
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not load student');
    } finally {
      setLoading(false);
    }
  }, [id]);

  React.useEffect(() => {
    load();
  }, [load]);

  const s = data?.student;

  return (
    <ScreenLayout
      title="Student profile"
      subtitle={s?.name ?? `ID ${id}`}
      onBack={() => navigation.goBack()}
      refreshing={loading}
      onRefresh={load}>
      {loading && !s ? <ActivityIndicator color={PRIMARY} style={{ marginTop: 24 }} /> : null}
      {s ? (
        <>
          <Card>
            <DetailRow label="Student ID" value={s.id} />
            <DetailRow label="Name" value={s.name} />
            <DetailRow label="Mobile" value={s.mobile} />
            <DetailRow label="Email" value={s.email} />
            <DetailRow label="Course" value={s.course_name} />
            <DetailRow label="Batch" value={s.batch} />
            <DetailRow label="School" value={s.school_name} />
            <DetailRow label="Fees balance" value={s.balance_fees} />
          </Card>
          <Card title="Recent attendance">
            {(data?.recent_attendance ?? []).map((r: any) => (
              <ListRow
                key={`${r.date}-${r.sid}`}
                title={r.date}
                subtitle={`${r.status} · ${r.course}/${r.batch}`}
              />
            ))}
            {!(data?.recent_attendance ?? []).length ? (
              <DetailRow label="Records" value="No recent attendance" />
            ) : null}
          </Card>
        </>
      ) : null}
    </ScreenLayout>
  );
}
