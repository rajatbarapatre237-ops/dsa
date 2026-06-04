import React, { useCallback, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import DetailRow from '../components/DetailRow';
import { LmsApi } from '../api/lms';

export default function TodayAttendanceScreen() {
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

  const att = data?.today_attendance;
  const child = data?.child;

  return (
    <ScreenLayout
      title="Today's attendance"
      subtitle={child?.name ?? 'Your child'}
      onBack={() => navigation.navigate('AttendanceHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <DetailRow label="Date" value={att?.date} />
        <DetailRow label="Status" value={att?.status} />
        <DetailRow label="Entry time" value={att?.entry_time} />
        <DetailRow label="Exit time" value={att?.exit_time} />
        <DetailRow label="Course" value={child?.course_name} />
        <DetailRow label="Batch" value={child?.batch} />
      </Card>
    </ScreenLayout>
  );
}
