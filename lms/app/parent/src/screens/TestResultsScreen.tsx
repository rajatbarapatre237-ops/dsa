import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { Text, StyleSheet } from 'react-native';
import { useNavigation, useRoute, RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { FormPicker } from '../components/FormPicker';
import {
  MarksOverviewCard,
  TestResult,
  marksStats,
  uniqueSubjectNames,
} from '../components/MarksUi';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';
import { MarksStackParamList } from '../navigation/types';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

function sortByDateDesc(results: TestResult[]) {
  return [...results].sort((a, b) => String(b.test_date ?? '').localeCompare(String(a.test_date ?? '')));
}

export default function TestResultsScreen({ allMarks }: { allMarks?: boolean }) {
  const navigation = useNavigation<NativeStackNavigationProp<MarksStackParamList>>();
  const route = useRoute<RouteProp<MarksStackParamList, 'TestResults' | 'AllTestMarks'>>();
  const initialSubject =
    route.name === 'TestResults' ? (route.params?.subjectName ?? null) : route.params?.subjectName ?? null;

  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<TestResult[]>([]);
  const [selectedSubject, setSelectedSubject] = useState<string | null>(initialSubject);

  useEffect(() => {
    setSelectedSubject(initialSubject);
  }, [initialSubject]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = allMarks ? await LmsApi.allTestMarks() : await LmsApi.classTestResults();
        setItems(res.results ?? []);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [allMarks, beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(() => load());

  const sorted = useMemo(() => sortByDateDesc(items), [items]);
  const subjects = useMemo(() => uniqueSubjectNames(sorted), [sorted]);
  const subjectOptions = useMemo(
    () => [{ label: 'All subjects', value: '' }, ...subjects.map(name => ({ label: name, value: name }))],
    [subjects],
  );
  const filtered = useMemo(() => {
    if (!selectedSubject) return sorted;
    return sorted.filter(
      r => (String(r.subject_name ?? 'General').trim() || 'General') === selectedSubject,
    );
  }, [selectedSubject, sorted]);
  const stats = marksStats(filtered);

  const title = allMarks
    ? selectedSubject
      ? `${selectedSubject} marks`
      : 'All Test Marks'
    : selectedSubject
      ? `${selectedSubject} results`
      : 'Class Test Results';

  return (
    <ScreenLayout
      title={title}
      subtitle="Filter by subject and tap for details"
      onBack={() => navigation.navigate('MarksHub')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <MarksOverviewCard
        total={stats.total}
        passed={stats.passed}
        failed={stats.failed}
        averagePercent={stats.averagePercent}
        passRate={stats.passRate}
      />

      {subjects.length > 0 ? (
        <FormPicker
          label="Subject"
          value={selectedSubject ?? ''}
          options={subjectOptions}
          onChange={value => setSelectedSubject(value || null)}
          placeholder="All subjects"
        />
      ) : null}

      <Card title={selectedSubject ? `${selectedSubject} tests` : 'All results'}>
        <DataList
          loading={loading}
          items={filtered}
          emptyText={selectedSubject ? `No tests for ${selectedSubject}` : 'No test results yet'}
          renderItem={(r: TestResult) => (
            <ListRow
              title={r.test_name ?? 'Class test'}
              subtitle={`${r.test_date ?? '—'} · ${r.subject_name ?? 'General'} · ${r.marks_obtained ?? '—'}/${r.total_marks ?? '—'}`}
              onPress={() => navigation.navigate('ClassTestResultDetail', { result: r as Record<string, unknown> })}
            />
          )}
        />
      </Card>

      {selectedSubject ? (
        <Text style={styles.hint}>Showing test results for {selectedSubject}</Text>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  hint: {
    fontSize: 12,
    color: theme.muted,
    textAlign: 'center',
    marginBottom: 8,
  },
});
