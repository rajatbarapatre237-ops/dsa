import React, { useCallback, useMemo, useState } from 'react';
import { FlatList, Pressable, StyleSheet, Text, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { ActivityIndicator, Alert } from 'react-native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import ListRow from '../components/ListRow';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';

export default function SalaryScreen() {
  const navigation = useNavigation<any>();
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<any[]>([]);
  const [error, setError] = useState<string | null>(null);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      setError(null);
      try {
        const res: any = await LmsApi.salary();
        setItems(res.salary ?? []);
        markHasData();
      } catch (e: any) {
        setItems([]);
        const msg = e?.message ?? 'Could not load salary records';
        setError(msg);
        Alert.alert('Error', msg);
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(load);

  const latest = useMemo(() => items?.[0] ?? null, [items]);

  return (
    <ScreenLayout
      title="View Salary"
      subtitle="Account"
      onBack={() => navigation.navigate('AccountHome')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <Card>
        {latest ? (
          <>
            <Text style={styles.latestTitle}>Latest</Text>
            <ListRow
              title={String(latest.month ?? latest.date ?? 'Salary')}
              subtitle="Most recent record"
              right={`₹${latest.amount ?? latest.salary ?? '—'}`}
            />
            <View style={styles.divider} />
          </>
        ) : null}

        {error ? (
          <>
            <Text style={styles.errorText}>{error}</Text>
            <Pressable style={styles.retryBtn} onPress={load} disabled={loading}>
              <Text style={styles.retryText}>{loading ? 'Loading…' : 'Retry'}</Text>
            </Pressable>
          </>
        ) : loading ? (
          <View style={styles.loadingWrap}>
            <ActivityIndicator color={PRIMARY} size="large" />
          </View>
        ) : items.length === 0 ? (
          <View style={styles.emptyWrap}>
            <Text style={styles.emptyTitle}>No salary records</Text>
            <Text style={styles.emptySub}>Salary history will appear here once it’s added.</Text>
          </View>
        ) : (
          <FlatList
            data={items}
            keyExtractor={(_, i) => String(items?.[i]?.srno ?? items?.[i]?.id ?? i)}
            renderItem={({ item }) => (
              <ListRow
                title={String(item.month ?? item.date ?? item.id ?? 'Salary')}
                subtitle="Recorded salary"
                right={`₹${item.amount ?? item.salary ?? '—'}`}
              />
            )}
            scrollEnabled={false}
          />
        )}
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  latestTitle: { fontSize: 12, color: theme.muted, fontWeight: '800', textTransform: 'uppercase', marginBottom: 10 },
  divider: { height: StyleSheet.hairlineWidth, backgroundColor: '#e2e8f0', marginVertical: 14 },
  loadingWrap: { paddingVertical: 18, alignItems: 'center', justifyContent: 'center' },
  errorText: { color: theme.danger, fontWeight: '600', marginBottom: 12, textAlign: 'center' },
  retryBtn: { alignItems: 'center', justifyContent: 'center', paddingVertical: 12, backgroundColor: '#f1f5f9', borderRadius: 10 },
  retryText: { color: theme.text, fontWeight: '700' },
  emptyWrap: { paddingVertical: 22, alignItems: 'center', justifyContent: 'center' },
  emptyTitle: { fontSize: 16, fontWeight: '800', color: theme.text, marginBottom: 6 },
  emptySub: { fontSize: 13, color: theme.muted, textAlign: 'center', lineHeight: 18, paddingHorizontal: 16 },
});
