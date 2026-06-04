import React, { useCallback, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import DetailRow from '../components/DetailRow';
import { LmsApi } from '../api/lms';

export default function CoursesScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState<any>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      setData(await LmsApi.courses());
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  const c = data?.current_course;
  const p = data?.profile;
  const taken = data?.courses_taken;

  return (
    <ScreenLayout
      title="Courses"
      subtitle="Your enrollment"
      onBack={() => navigation.navigate('AcademicsHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card title="Current course">
        <DetailRow label="Course" value={c?.course_name ?? p?.course_name} />
        <DetailRow label="Fees" value={c?.course_fees ?? p?.course_fees} />
        <DetailRow label="Batch" value={p?.batch} />
        <DetailRow label="Balance fees" value={p?.balance_fees} />
      </Card>
      {taken ? (
        <Card title="Courses taken">
          <DetailRow label="Record" value={typeof taken === 'object' ? JSON.stringify(taken) : String(taken)} />
        </Card>
      ) : null}
    </ScreenLayout>
  );
}
