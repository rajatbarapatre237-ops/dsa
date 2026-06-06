import React, { useCallback, useMemo, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { MarksOverviewCard, TestResult, marksStats } from '../components/MarksUi';
import { LmsApi } from '../api/lms';
import { AssignmentsStackParamList } from '../navigation/types';

function sortByDateDesc(results: TestResult[]) {
  return [...results].sort((a, b) => String(b.test_date ?? '').localeCompare(String(a.test_date ?? '')));
}

export default function TestResultsScreen({ allMarks }: { allMarks?: boolean }) {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<TestResult[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = allMarks ? await LmsApi.allTestMarks() : await LmsApi.classTestResults();
      setItems(res.results ?? []);
    } finally {
      setLoading(false);
    }
  }, [allMarks]);

  React.useEffect(() => {
    load();
  }, [load]);

  const sorted = useMemo(() => sortByDateDesc(items), [items]);
  const stats = marksStats(sorted);

  return (
    <ScreenLayout
      title={allMarks ? 'All Test Marks' : 'Class Test Results'}
      subtitle="Tap for details"
      onBack={() => navigation.navigate('AssignmentsHub')}
      refreshing={loading}
      onRefresh={load}>
      <MarksOverviewCard
        total={stats.total}
        passed={stats.passed}
        failed={stats.failed}
        averagePercent={stats.averagePercent}
        passRate={stats.passRate}
      />

      <Card title="All results">
        <DataList
          loading={loading}
          items={sorted}
          emptyText="No test results yet"
          renderItem={(r: TestResult) => (
            <ListRow
              title={r.test_name ?? 'Class test'}
              subtitle={`${r.test_date ?? '—'} · ${r.marks_obtained ?? '—'}/${r.total_marks ?? '—'}`}
              onPress={() => navigation.navigate('ClassTestResultDetail', { result: r as Record<string, unknown> })}
            />
          )}
        />
      </Card>
    </ScreenLayout>
  );
}
