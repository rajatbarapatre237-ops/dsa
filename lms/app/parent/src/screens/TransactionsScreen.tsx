import React, { useCallback, useMemo, useState } from 'react';
import { View, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { HeroCard, SectionTitle } from '../components/DashboardUi';
import { EmptyStateCard, FeeSummaryCard, TransactionListItem } from '../components/FeesUi';
import { formatStudentDisplayId } from '../utils/studentId';
import { LmsApi } from '../api/lms';
import { APP_SUBTITLE } from '../config';
import { theme } from '../ui/theme';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';

export default function TransactionsScreen() {
  const navigation = useNavigation<any>();
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [child, setChild] = useState<any>(null);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const result = await Promise.allSettled([LmsApi.dashboard(), LmsApi.transactions()]);
        if (result[0].status === 'fulfilled') {
          const dashboard = result[0].value.dashboard ?? result[0].value;
          setChild(dashboard?.child ?? null);
        }
        if (result[1].status === 'fulfilled') {
          setItems(result[1].value.transactions ?? []);
        } else {
          setItems([]);
        }
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(load);

  const totalFees = Number(child?.course_fees ?? 0);
  const balance = Number(child?.balance_fees ?? 0);
  const paid = useMemo(() => Math.max(0, totalFees - balance), [totalFees, balance]);

  return (
    <ScreenLayout
      title="Fee history"
      subtitle="Payment records"
      onBack={() => navigation.navigate('HomeHub')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <HeroCard
        eyebrow={APP_SUBTITLE}
        title={child?.name ?? 'Your child'}
        subtitle="Fee payments and balance"
        avatarLabel={child?.name}
        chips={[
          { label: formatStudentDisplayId(child?.id) ?? 'ID —', icon: 'card-outline' },
          { label: child?.course_name ?? 'Course —', icon: 'school-outline' },
        ]}
      />

      <FeeSummaryCard totalFees={totalFees} balance={balance} paid={paid} />

      <SectionTitle>Payment history</SectionTitle>
      {items.length > 0 ? (
        <View style={styles.listCard}>
          {items.map((item, index) => (
            <View key={item.id ?? index}>
              <TransactionListItem item={item} />
              {index < items.length - 1 ? <View style={styles.divider} /> : null}
            </View>
          ))}
        </View>
      ) : (
        <EmptyStateCard
          icon="wallet-outline"
          title="No transactions yet"
          message="Fee payment history for your child will appear here once payments are recorded."
        />
      )}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  listCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingVertical: 4,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.06,
    shadowRadius: 14,
    elevation: 3,
  },
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: theme.border,
  },
});
