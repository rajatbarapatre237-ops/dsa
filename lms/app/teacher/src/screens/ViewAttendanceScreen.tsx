import React, { useCallback, useMemo, useState } from 'react';
import { Text, StyleSheet, View, TextInput, Pressable } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { FormPicker } from '../components/FormPicker';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { formatStudentBatch } from '../utils/student';

type AttendanceRecord = {
  date: string;
  sid: string | number;
  status: string;
  course?: string;
  batch?: string;
  student_name?: string;
};

function isPresent(status: string) {
  return (status ?? '').trim().toLowerCase() === 'present';
}

function statusLabel(status: string) {
  return isPresent(status) ? 'Present' : 'Absent';
}

function uniqueSorted(values: string[]) {
  return [...new Set(values.filter(Boolean))].sort((a, b) => a.localeCompare(b));
}

function currentMonthValue() {
  return new Date().toISOString().slice(0, 7);
}

function formatMonthLabel(month: string) {
  if (month === 'all') return 'All months';
  const [year, monthNum] = month.split('-').map(Number);
  if (!year || !monthNum) return month;
  return new Date(year, monthNum - 1, 1).toLocaleString('en-IN', {
    month: 'long',
    year: 'numeric',
  });
}

function AttendanceRow({ record }: { record: AttendanceRecord }) {
  const present = isPresent(record.status);

  return (
    <View style={styles.recordRow}>
      <View style={styles.recordMain}>
        <Text style={styles.recordName}>
          {record.student_name?.trim() || `Student ${record.sid}`}
        </Text>
        <Text style={styles.recordId}>SID {record.sid}</Text>
        <Text style={styles.recordMeta}>
          {record.course}
          {formatStudentBatch(record.batch) ? ` · ${formatStudentBatch(record.batch)}` : ''}
        </Text>
      </View>
      <View style={[styles.badge, present ? styles.badgePresent : styles.badgeAbsent]}>
        <Text style={[styles.badgeText, present ? styles.badgeTextPresent : styles.badgeTextAbsent]}>
          {statusLabel(record.status)}
        </Text>
      </View>
    </View>
  );
}

export default function ViewAttendanceScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<AttendanceRecord[]>([]);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [courseFilter, setCourseFilter] = useState('');
  const [batchFilter, setBatchFilter] = useState('');
  const [monthFilter, setMonthFilter] = useState(currentMonthValue());
  const [monthOptions, setMonthOptions] = useState<{ label: string; value: string }[]>([
    { label: formatMonthLabel(currentMonthValue()), value: currentMonthValue() },
    { label: 'All months', value: 'all' },
  ]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.attendance(monthFilter);
      setItems(res.records ?? []);
      const months: string[] = res.available_months ?? [];
      setMonthOptions([
        { label: 'All months', value: 'all' },
        ...months.map((month: string) => ({ label: formatMonthLabel(month), value: month })),
      ]);
    } catch {
      setItems([]);
    } finally {
      setLoading(false);
    }
  }, [monthFilter]);

  React.useEffect(() => {
    load();
  }, [load]);

  const periodLabel = formatMonthLabel(monthFilter);

  const courseOptions = useMemo(() => {
    const courses = uniqueSorted(items.map(r => String(r.course ?? '').trim()));
    return [{ label: 'All courses', value: '' }, ...courses.map(c => ({ label: c, value: c }))];
  }, [items]);

  const batchOptions = useMemo(() => {
    const scoped = courseFilter
      ? items.filter(r => String(r.course ?? '').trim() === courseFilter)
      : items;
    const batches = uniqueSorted(
      scoped.map(r => formatStudentBatch(r.batch)).filter((b): b is string => !!b),
    );
    return [{ label: 'All batches', value: '' }, ...batches.map(b => ({ label: b, value: b }))];
  }, [items, courseFilter]);

  const filteredItems = useMemo(() => {
    const query = search.trim().toLowerCase();
    return items.filter(record => {
      const course = String(record.course ?? '').trim();
      const batch = formatStudentBatch(record.batch);
      const sid = String(record.sid ?? '');
      const name = String(record.student_name ?? '').toLowerCase();
      const present = isPresent(record.status);

      if (courseFilter && course !== courseFilter) return false;
      if (batchFilter && batch !== batchFilter) return false;
      if (statusFilter === 'present' && !present) return false;
      if (statusFilter === 'absent' && present) return false;
      if (!query) return true;

      return (
        sid.includes(query) ||
        name.includes(query) ||
        course.toLowerCase().includes(query) ||
        (batch ?? '').toLowerCase().includes(query)
      );
    });
  }, [items, search, statusFilter, courseFilter, batchFilter]);

  const groupedByDate = useMemo(() => {
    const groups = new Map<string, AttendanceRecord[]>();
    filteredItems.forEach(record => {
      const date = record.date;
      const list = groups.get(date) ?? [];
      list.push(record);
      groups.set(date, list);
    });
    return [...groups.entries()].sort((a, b) => b[0].localeCompare(a[0]));
  }, [filteredItems]);

  const hasFilters = !!(search.trim() || statusFilter || courseFilter || batchFilter);

  function clearFilters() {
    setSearch('');
    setStatusFilter('');
    setCourseFilter('');
    setBatchFilter('');
  }

  return (
    <ScreenLayout
      title="View Attendance"
      subtitle={periodLabel}
      onBack={() => navigation.navigate('AttendanceHub')}
      refreshing={loading}
      onRefresh={load}>
      <Card>
        <FormPicker
          label="Month"
          value={monthFilter}
          options={monthOptions}
          onChange={value => {
            setMonthFilter(value);
            setCourseFilter('');
            setBatchFilter('');
          }}
        />
        {monthFilter === 'all' ? (
          <Text style={styles.monthHint}>Showing the latest 500 records across all months</Text>
        ) : null}

        <View style={styles.searchWrap}>
          <AppIcon name="search" size={18} color={theme.muted} />
          <TextInput
            style={styles.searchInput}
            value={search}
            onChangeText={setSearch}
            placeholder="Search by name or student ID"
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
          label="Status"
          value={statusFilter}
          options={[
            { label: 'All statuses', value: '' },
            { label: 'Present', value: 'present' },
            { label: 'Absent', value: 'absent' },
          ]}
          onChange={setStatusFilter}
        />

        <View style={styles.filterRow}>
          <View style={styles.filterField}>
            <FormPicker
              label="Course"
              value={courseFilter}
              options={courseOptions}
              onChange={value => {
                setCourseFilter(value);
                setBatchFilter('');
              }}
              placeholder="All courses"
            />
          </View>
          <View style={styles.filterField}>
            <FormPicker
              label="Batch"
              value={batchFilter}
              options={batchOptions}
              onChange={setBatchFilter}
              placeholder="All batches"
              disabled={batchOptions.length <= 1}
            />
          </View>
        </View>

        <View style={styles.metaRow}>
          <Text style={styles.metaText}>
            Showing {filteredItems.length} of {items.length} records
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
          <Text style={styles.empty}>Loading attendance…</Text>
        </Card>
      ) : groupedByDate.length > 0 ? (
        groupedByDate.map(([date, records]) => (
          <Card key={date} title={date}>
            {records.map((record, index) => (
              <View key={`${date}-${record.sid}-${index}`}>
                {index > 0 ? <View style={styles.divider} /> : null}
                <AttendanceRow record={record} />
              </View>
            ))}
          </Card>
        ))
      ) : (
        <Card>
          <Text style={styles.empty}>
            {hasFilters
              ? 'No records match these filters'
              : `No attendance records for ${periodLabel.toLowerCase()}`}
          </Text>
        </Card>
      )}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  monthHint: {
    fontSize: 12,
    color: theme.muted,
    marginBottom: 12,
    marginTop: -6,
  },
  searchWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 10,
    paddingHorizontal: 12,
    paddingVertical: 10,
    backgroundColor: '#fff',
    marginBottom: 12,
    gap: 8,
  },
  searchInput: {
    flex: 1,
    fontSize: 16,
    color: theme.text,
    padding: 0,
  },
  filterRow: {
    flexDirection: 'row',
    gap: 10,
  },
  filterField: {
    flex: 1,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 2,
  },
  metaText: {
    fontSize: 12,
    color: theme.muted,
    fontWeight: '600',
  },
  clearText: {
    fontSize: 12,
    color: PRIMARY,
    fontWeight: '700',
  },
  recordRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 10,
    gap: 12,
  },
  recordMain: {
    flex: 1,
  },
  recordName: {
    fontSize: 15,
    fontWeight: '700',
    color: theme.text,
  },
  recordId: {
    fontSize: 12,
    fontWeight: '600',
    color: theme.muted,
    marginTop: 2,
  },
  recordMeta: {
    fontSize: 12,
    color: theme.muted,
    marginTop: 3,
  },
  badge: {
    borderRadius: 20,
    paddingHorizontal: 10,
    paddingVertical: 5,
  },
  badgePresent: {
    backgroundColor: '#ecfdf3',
  },
  badgeAbsent: {
    backgroundColor: '#fef2f2',
  },
  badgeText: {
    fontSize: 12,
    fontWeight: '700',
  },
  badgeTextPresent: {
    color: theme.success,
  },
  badgeTextAbsent: {
    color: theme.danger,
  },
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: '#e2e8f0',
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 8,
  },
});
