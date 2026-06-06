import React, { useCallback, useMemo, useState } from 'react';
import { View, StyleSheet } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { SectionHeader, StudentContextCard } from '../components/DashboardUi';
import {
  EmptyStateCard,
  FeeSummaryCard,
  TransactionListItem,
} from '../components/AcademicsUi';
import { useStudentContext } from '../hooks/useStudentContext';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';

export default function TransactionsScreen() {
  const navigation = useNavigation<any>();
  const ctx = useStudentContext();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const result = await Promise.allSettled([LmsApi.transactions()]);
      if (result[0].status === 'fulfilled') {
        setItems(result[0].value.transactions ?? []);
      } else {
        setItems([]);
      }
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  const refresh = useCallback(async () => {
    await Promise.all([ctx.refresh(), load()]);
  }, [ctx, load]);

  const profile = ctx.profile;
  const totalFees = Number(profile?.course_fees ?? 0);
  const balance = Number(profile?.balance_fees ?? 0);
  const paid = useMemo(() => Math.max(0, totalFees - balance), [totalFees, balance]);

  return (
    <ScreenLayout
      title="Transactions"
      subtitle="Fee payments"
      onBack={() => navigation.navigate('AcademicsHub')}
      refreshing={loading || ctx.loading}
      onRefresh={refresh}>
      <StudentContextCard
        name={ctx.name}
        studentId={ctx.studentId}
        course={ctx.course}
        batch={ctx.batch}
        profile={ctx.profile}
        attendanceSummary={ctx.attendanceSummary}
        monthRecords={ctx.monthRecords}
      />

      <FeeSummaryCard totalFees={totalFees} balance={balance} paid={paid} />

      <SectionHeader title="Payment history" />
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
          message="Your fee payment history will appear here once payments are recorded."
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
