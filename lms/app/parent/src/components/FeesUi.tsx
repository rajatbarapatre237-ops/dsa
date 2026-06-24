import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export function FeeSummaryCard({
  totalFees,
  balance,
  paid,
}: {
  totalFees?: string | number | null;
  balance?: string | number | null;
  paid?: number;
}) {
  return (
    <View style={styles.summaryCard}>
      <View style={styles.summaryIcon}>
        <AppIcon name="cash-outline" family="ionicons" size={22} color={PRIMARY} />
      </View>
      <View style={styles.summaryBody}>
        <Text style={styles.summaryValue}>{formatMoney(paid ?? 0)}</Text>
        <Text style={styles.summaryLabel}>
          Paid of {formatMoney(totalFees)} · Balance {formatMoney(balance)}
        </Text>
      </View>
    </View>
  );
}

export function TransactionListItem({ item }: { item: any }) {
  const amount = item.amount ?? item.paid;
  const date = item.date ?? item.created_at ?? '—';
  const reason = String(item.reason ?? item.mode ?? 'Fee payment').trim();

  return (
    <View style={styles.txnRow}>
      <View style={styles.txnIcon}>
        <AppIcon name="receipt-outline" family="ionicons" size={18} color={PRIMARY} />
      </View>
      <View style={styles.txnBody}>
        <Text style={styles.txnTitle} numberOfLines={1}>
          {reason}
        </Text>
        <Text style={styles.txnMeta}>{String(date)}</Text>
      </View>
      <Text style={styles.txnAmount}>{formatMoney(amount)}</Text>
    </View>
  );
}

export function EmptyStateCard({
  icon,
  title,
  message,
}: {
  icon: string;
  title: string;
  message: string;
}) {
  return (
    <View style={styles.emptyCard}>
      <View style={styles.emptyIcon}>
        <AppIcon name={icon} family="ionicons" size={28} color={PRIMARY} />
      </View>
      <Text style={styles.emptyTitle}>{title}</Text>
      <Text style={styles.emptyMessage}>{message}</Text>
    </View>
  );
}

function formatMoney(value?: string | number | null) {
  const num = Number(value ?? 0);
  if (Number.isNaN(num)) return '—';
  return `₹${num.toLocaleString('en-IN')}`;
}

const styles = StyleSheet.create({
  summaryCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    gap: 14,
  },
  summaryIcon: {
    width: 50,
    height: 50,
    borderRadius: 16,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  summaryBody: { flex: 1 },
  summaryValue: { fontSize: 24, fontWeight: '800', color: theme.text },
  summaryLabel: { fontSize: 12, color: theme.muted, marginTop: 4, lineHeight: 17 },
  txnRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    gap: 10,
  },
  txnIcon: {
    width: 36,
    height: 36,
    borderRadius: 12,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  txnBody: { flex: 1 },
  txnTitle: { fontSize: 14, fontWeight: '800', color: theme.text },
  txnMeta: { fontSize: 11, color: theme.muted, marginTop: 3 },
  txnAmount: { fontSize: 14, fontWeight: '800', color: theme.success },
  emptyCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 28,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'center',
  },
  emptyIcon: {
    width: 56,
    height: 56,
    borderRadius: 18,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  emptyTitle: { fontSize: 16, fontWeight: '800', color: theme.text },
  emptyMessage: { fontSize: 13, color: theme.muted, marginTop: 6, textAlign: 'center', lineHeight: 18 },
});
