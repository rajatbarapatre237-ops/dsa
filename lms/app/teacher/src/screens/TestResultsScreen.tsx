import React, { useCallback, useMemo, useState } from 'react';
import {
  ActivityIndicator,
  FlatList,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { useThemeColors } from '../ui/useThemeColors';
import { WorkStackParamList } from '../navigation/types';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';

type TestResult = {
  id?: number;
  test_id?: number;
  student_id?: number | string;
  student_name?: string;
  test_name?: string;
  test_date?: string;
  marks_obtained?: number | string;
  total_marks?: number | string;
  passing_marks?: number | string;
  course_name?: string;
  subject_name?: string;
};

function resultStatus(result: TestResult): 'pass' | 'fail' | 'unknown' {
  const marks = result.marks_obtained;
  const passing = result.passing_marks;
  if (marks == null || marks === '' || passing == null || passing === '') {
    return 'unknown';
  }
  return Number(marks) >= Number(passing) ? 'pass' : 'fail';
}

function studentLabel(result: TestResult) {
  const name = String(result.student_name ?? '').trim();
  if (name) return name;
  return `Student ${result.student_id ?? '—'}`;
}

function ResultRow({
  result,
  onPress,
}: {
  result: TestResult;
  onPress: () => void;
}) {
  const status = resultStatus(result);
  const marks =
    result.marks_obtained != null && result.total_marks != null
      ? `${result.marks_obtained}/${result.total_marks}`
      : '—';

  return (
    <Pressable style={styles.resultRow} onPress={onPress}>
      <View style={styles.avatar}>
        <Text style={styles.avatarText}>{studentLabel(result).charAt(0).toUpperCase()}</Text>
      </View>
      <View style={styles.resultMain}>
        <Text style={styles.resultName} numberOfLines={1}>
          {studentLabel(result)}
        </Text>
        <Text style={styles.resultMeta} numberOfLines={1}>
          {result.test_name}
          {result.test_date ? ` · ${result.test_date}` : ''}
        </Text>
        <Text style={styles.resultMarks}>Marks: {marks}</Text>
      </View>
      <View
        style={[
          styles.statusBadge,
          status === 'pass' ? styles.statusPass : status === 'fail' ? styles.statusFail : styles.statusUnknown,
        ]}>
        <Text
          style={[
            styles.statusText,
            status === 'pass'
              ? styles.statusTextPass
              : status === 'fail'
                ? styles.statusTextFail
                : styles.statusTextUnknown,
          ]}>
          {status === 'pass' ? 'Pass' : status === 'fail' ? 'Fail' : '—'}
        </Text>
      </View>
    </Pressable>
  );
}

export default function TestResultsScreen() {
  const colors = useThemeColors();
  const navigation = useNavigation<NativeStackNavigationProp<WorkStackParamList>>();
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<TestResult[]>([]);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [testFilter, setTestFilter] = useState('');

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.testResults();
        setItems(res.results ?? []);
        markHasData();
      } catch {
        setItems([]);
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(load);

  const testOptions = useMemo(() => {
    const names = [...new Set(items.map(r => String(r.test_name ?? '').trim()).filter(Boolean))].sort();
    return [{ label: 'All tests', value: '' }, ...names.map(name => ({ label: name, value: name }))];
  }, [items]);

  const filteredItems = useMemo(() => {
    const query = search.trim().toLowerCase();
    return items.filter(result => {
      const status = resultStatus(result);
      const testName = String(result.test_name ?? '').trim();
      const studentName = String(result.student_name ?? '').toLowerCase();
      const studentId = String(result.student_id ?? '');

      if (testFilter && testName !== testFilter) return false;
      if (statusFilter === 'pass' && status !== 'pass') return false;
      if (statusFilter === 'fail' && status !== 'fail') return false;
      if (!query) return true;

      return (
        studentName.includes(query) ||
        studentId.includes(query) ||
        testName.toLowerCase().includes(query) ||
        String(result.subject_name ?? '').toLowerCase().includes(query)
      );
    });
  }, [items, search, statusFilter, testFilter]);

  const stats = useMemo(() => {
    const pass = filteredItems.filter(r => resultStatus(r) === 'pass').length;
    const fail = filteredItems.filter(r => resultStatus(r) === 'fail').length;
    return { pass, fail, total: filteredItems.length };
  }, [filteredItems]);

  const hasFilters = !!(search.trim() || statusFilter || testFilter);

  function clearFilters() {
    setSearch('');
    setStatusFilter('');
    setTestFilter('');
  }

  const listHeader = (
    <>
      <Card>
        <View style={styles.statsRow}>
          <View style={styles.statBox}>
            <Text style={styles.statValue}>{stats.total}</Text>
            <Text style={styles.statLabel}>Showing</Text>
          </View>
          <View style={[styles.statBox, styles.statPassBox]}>
            <Text style={[styles.statValue, styles.statPassText]}>{stats.pass}</Text>
            <Text style={styles.statLabel}>Pass</Text>
          </View>
          <View style={[styles.statBox, styles.statFailBox]}>
            <Text style={[styles.statValue, styles.statFailText]}>{stats.fail}</Text>
            <Text style={styles.statLabel}>Fail</Text>
          </View>
        </View>

        <View style={[styles.searchWrap, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder }]}>
          <AppIcon name="search" size={18} color={colors.muted} />
          <TextInput
            style={[styles.searchInput, { color: colors.inputText }]}
            value={search}
            onChangeText={setSearch}
            placeholder="Search student, ID, or test"
            placeholderTextColor={theme.muted}
            autoCapitalize="none"
            autoCorrect={false}
          />
          {search ? (
            <Pressable onPress={() => setSearch('')} hitSlop={8}>
              <AppIcon name="close-circle" size={18} color={theme.muted} />
            </Pressable>
          ) : null}
        </View>

        <FormPicker
          label="Result"
          value={statusFilter}
          options={[
            { label: 'All results', value: '' },
            { label: 'Pass only', value: 'pass' },
            { label: 'Fail only', value: 'fail' },
          ]}
          onChange={setStatusFilter}
        />

        <FormPicker label="Test" value={testFilter} options={testOptions} onChange={setTestFilter} />

        <View style={styles.metaRow}>
          <Text style={styles.metaText}>
            {filteredItems.length} of {items.length} records
          </Text>
          {hasFilters ? (
            <Pressable onPress={clearFilters}>
              <Text style={styles.clearText}>Clear filters</Text>
            </Pressable>
          ) : null}
        </View>
      </Card>

      {loading ? (
        <Card>
          <View style={styles.loadingWrap}>
            <ActivityIndicator color={PRIMARY} />
            <Text style={styles.loadingText}>Loading results…</Text>
          </View>
        </Card>
      ) : null}
    </>
  );

  return (
    <ScreenLayout
      title="Class Test Results"
      subtitle="Search & filter student marks"
      onBack={() => navigation.navigate('WorkHub')}
      scroll={false}>
      <FlatList
        style={styles.flex}
        data={loading ? [] : filteredItems}
        keyExtractor={(item, index) => `${item.test_id ?? 't'}-${item.student_id ?? index}`}
        renderItem={({ item }) => (
          <ResultRow
            result={item}
            onPress={() => navigation.navigate('ClassTestResultDetail', { result: item })}
          />
        )}
        ListHeaderComponent={listHeader}
        ListEmptyComponent={
          !loading ? (
            <Card>
              <Text style={styles.empty}>
                {hasFilters ? 'No results match these filters' : 'No test results yet'}
              </Text>
            </Card>
          ) : null
        }
        ItemSeparatorComponent={() => <View style={styles.separator} />}
        contentContainerStyle={styles.listContent}
        keyboardShouldPersistTaps="handled"
        keyboardDismissMode="on-drag"
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={() => load({ showRefresh: true })}
            tintColor={PRIMARY}
          />
        }
        initialNumToRender={20}
        maxToRenderPerBatch={25}
        windowSize={10}
        removeClippedSubviews
      />
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  flex: { flex: 1 },
  listContent: { padding: 16, paddingBottom: 100 },
  statsRow: { flexDirection: 'row', gap: 10, marginBottom: 14 },
  statBox: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: 'center',
  },
  statPassBox: { backgroundColor: '#ecfdf3' },
  statFailBox: { backgroundColor: '#fef2f2' },
  statValue: { fontSize: 20, fontWeight: '800', color: theme.text },
  statPassText: { color: theme.success },
  statFailText: { color: theme.danger },
  statLabel: { fontSize: 11, color: theme.muted, marginTop: 4, fontWeight: '600' },
  searchWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderRadius: 10,
    paddingHorizontal: 12,
    paddingVertical: 10,
    marginBottom: 12,
    gap: 8,
  },
  searchInput: { flex: 1, fontSize: 16, padding: 0 },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 2,
  },
  metaText: { fontSize: 12, color: theme.muted, fontWeight: '600' },
  clearText: { fontSize: 12, color: PRIMARY, fontWeight: '700' },
  loadingWrap: { alignItems: 'center', paddingVertical: 16, gap: 8 },
  loadingText: { fontSize: 14, color: theme.muted },
  resultRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.card,
    borderRadius: 14,
    padding: 14,
    gap: 12,
    shadowColor: '#000',
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 1,
  },
  avatar: {
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: '#e8f2fb',
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: { fontSize: 16, fontWeight: '800', color: PRIMARY },
  resultMain: { flex: 1 },
  resultName: { fontSize: 15, fontWeight: '700', color: theme.text },
  resultMeta: { fontSize: 12, color: theme.muted, marginTop: 2 },
  resultMarks: { fontSize: 12, color: theme.text, marginTop: 4, fontWeight: '600' },
  statusBadge: { borderRadius: 20, paddingHorizontal: 10, paddingVertical: 5 },
  statusPass: { backgroundColor: '#ecfdf3' },
  statusFail: { backgroundColor: '#fef2f2' },
  statusUnknown: { backgroundColor: '#f1f5f9' },
  statusText: { fontSize: 12, fontWeight: '700' },
  statusTextPass: { color: theme.success },
  statusTextFail: { color: theme.danger },
  statusTextUnknown: { color: theme.muted },
  separator: { height: 10 },
  empty: { fontSize: 14, color: theme.muted, textAlign: 'center', paddingVertical: 8 },
});
