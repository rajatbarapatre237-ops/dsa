import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { Text, StyleSheet, View, TextInput, Pressable } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { FormPicker } from '../components/FormPicker';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { useThemeColors } from '../ui/useThemeColors';
import { StudentsStackParamList } from '../navigation/types';
import { formatStudentDisplayId, formatStudentSubtitle } from '../utils/student';

const PAGE_SIZE = 20;

type Pagination = {
  page: number;
  per_page: number;
  total: number;
  total_pages: number;
};

export default function StudentsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<StudentsStackParamList>>();
  const colors = useThemeColors();
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<any[]>([]);
  const [pagination, setPagination] = useState<Pagination>({
    page: 1,
    per_page: PAGE_SIZE,
    total: 0,
    total_pages: 1,
  });
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [courseFilter, setCourseFilter] = useState('');
  const [batchFilter, setBatchFilter] = useState('');
  const [page, setPage] = useState(1);
  const [courseOptions, setCourseOptions] = useState<{ label: string; value: string }[]>([
    { label: 'All courses', value: '' },
  ]);
  const [batchOptions, setBatchOptions] = useState<{ label: string; value: string }[]>([
    { label: 'All batches', value: '' },
  ]);

  useEffect(() => {
    const timer = setTimeout(() => setDebouncedSearch(search.trim()), 300);
    return () => clearTimeout(timer);
  }, [search]);

  useEffect(() => {
    setPage(1);
  }, [debouncedSearch, courseFilter, batchFilter]);

  useEffect(() => {
    LmsApi.formCourses()
      .then((res: any) => {
        const courses: string[] = res.courses ?? [];
        setCourseOptions([
          { label: 'All courses', value: '' },
          ...courses.map((name: string) => ({ label: name, value: name })),
        ]);
      })
      .catch(() => {
        /* ignore */
      });
  }, []);

  useEffect(() => {
    if (!courseFilter) {
      setBatchOptions([{ label: 'All batches', value: '' }]);
      return;
    }
    LmsApi.formBatches(courseFilter)
      .then((res: any) => {
        const batches: string[] = res.batches ?? [];
        setBatchOptions([
          { label: 'All batches', value: '' },
          ...batches.map((name: string) => ({ label: name, value: name })),
        ]);
      })
      .catch(() => {
        setBatchOptions([{ label: 'All batches', value: '' }]);
      });
  }, [courseFilter]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.students({
          page,
          per_page: PAGE_SIZE,
          search: debouncedSearch || undefined,
          course: courseFilter || undefined,
          batch: batchFilter || undefined,
        });
        setItems(res.students ?? []);
        setPagination(
          res.pagination ?? {
            page,
            per_page: PAGE_SIZE,
            total: (res.students ?? []).length,
            total_pages: 1,
          },
        );
        markHasData();
      } finally {
        endLoad();
      }
    },
    [page, debouncedSearch, courseFilter, batchFilter, beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(load);

  const hasFilters = !!(debouncedSearch || courseFilter || batchFilter);
  const rangeStart = pagination.total === 0 ? 0 : (pagination.page - 1) * pagination.per_page + 1;
  const rangeEnd = Math.min(pagination.page * pagination.per_page, pagination.total);
  const canGoPrev = pagination.page > 1;
  const canGoNext = pagination.page < pagination.total_pages;

  const pageLabel = useMemo(() => {
    if (pagination.total === 0) return 'No students';
    return `Showing ${rangeStart}-${rangeEnd} of ${pagination.total} students`;
  }, [pagination, rangeEnd, rangeStart]);

  function clearFilters() {
    setSearch('');
    setCourseFilter('');
    setBatchFilter('');
  }

  return (
    <ScreenLayout
      title="Students"
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <Card>
        <View style={[styles.searchWrap, { backgroundColor: colors.inputBg, borderColor: colors.inputBorder }]}>
          <AppIcon name="search" size={18} color={colors.muted} />
          <TextInput
            style={[styles.searchInput, { color: colors.inputText }]}
            value={search}
            onChangeText={setSearch}
            placeholder="Search by name or ID"
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
              compact
            />
          </View>
          <View style={styles.filterField}>
            <FormPicker
              label="Batch"
              value={batchFilter}
              options={batchOptions}
              onChange={setBatchFilter}
              placeholder="All batches"
              disabled={!courseFilter || batchOptions.length <= 1}
              compact
            />
          </View>
        </View>

        <View style={styles.metaRow}>
          <Text style={styles.metaText}>{pageLabel}</Text>
          {hasFilters ? (
            <Pressable onPress={clearFilters}>
              <Text style={styles.clearText}>Clear filters</Text>
            </Pressable>
          ) : null}
        </View>
      </Card>

      <Card>
        <DataList
          loading={loading}
          items={items}
          emptyText={hasFilters ? 'No students match these filters' : 'No students found'}
          renderItem={(s: any) => (
            <ListRow
              title={s.name}
              subtitle={formatStudentSubtitle(s)}
              right={formatStudentDisplayId(s.id) ?? '—'}
              onPress={() => navigation.navigate('StudentDetail', { id: String(s.id) })}
            />
          )}
        />

        {!loading && pagination.total_pages > 1 ? (
          <View style={styles.pagination}>
            <Pressable
              style={[styles.pageBtn, !canGoPrev && styles.pageBtnDisabled]}
              onPress={() => setPage(p => Math.max(1, p - 1))}
              disabled={!canGoPrev}>
              <AppIcon name="chevron-back" size={18} color={canGoPrev ? PRIMARY : theme.muted} />
              <Text style={[styles.pageBtnText, !canGoPrev && styles.pageBtnTextDisabled]}>Prev</Text>
            </Pressable>

            <Text style={styles.pageInfo}>
              Page {pagination.page} of {pagination.total_pages}
            </Text>

            <Pressable
              style={[styles.pageBtn, !canGoNext && styles.pageBtnDisabled]}
              onPress={() => setPage(p => Math.min(pagination.total_pages, p + 1))}
              disabled={!canGoNext}>
              <Text style={[styles.pageBtnText, !canGoNext && styles.pageBtnTextDisabled]}>Next</Text>
              <AppIcon name="chevron-forward" size={18} color={canGoNext ? PRIMARY : theme.muted} />
            </Pressable>
          </View>
        ) : null}
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
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
  searchInput: {
    flex: 1,
    fontSize: 16,
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
    flex: 1,
    paddingRight: 8,
  },
  clearText: {
    fontSize: 12,
    color: PRIMARY,
    fontWeight: '700',
  },
  pagination: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: '#e2e8f0',
    marginTop: 8,
    paddingTop: 12,
  },
  pageBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingVertical: 8,
    paddingHorizontal: 10,
    borderRadius: 10,
    backgroundColor: '#f8fafc',
  },
  pageBtnDisabled: {
    opacity: 0.5,
  },
  pageBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: PRIMARY,
  },
  pageBtnTextDisabled: {
    color: theme.muted,
  },
  pageInfo: {
    fontSize: 13,
    fontWeight: '600',
    color: theme.text,
  },
});
