import React from 'react';
import { View, Text, StyleSheet, Pressable, ViewStyle } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

export function AssignmentsSummaryCard({
  count,
  label,
  iconName = 'document-text-outline',
  iconFamily = 'ionicons',
  compact = false,
  style,
}: {
  count: number;
  label?: string;
  iconName?: string;
  iconFamily?: 'ionicons' | 'material';
  compact?: boolean;
  style?: ViewStyle;
}) {
  return (
    <View style={[styles.summaryCard, compact && styles.summaryCardCompact, style]}>
      <View style={[styles.summaryIcon, compact && styles.summaryIconCompact]}>
        <AppIcon name={iconName} family={iconFamily} size={compact ? 20 : 22} color={PRIMARY} />
      </View>
      <View style={styles.summaryBody}>
        <Text style={[styles.summaryValue, compact && styles.summaryValueCompact]}>{count}</Text>
        <Text style={[styles.summaryLabel, compact && styles.summaryLabelCompact]}>
          {label ?? `Active assignment${count === 1 ? '' : 's'} for your batch`}
        </Text>
      </View>
    </View>
  );
}

export function WorkSummaryCardsRow({
  assignmentsCount,
  notesCount,
}: {
  assignmentsCount: number;
  notesCount: number;
}) {
  return (
    <View style={styles.summaryRow}>
      <AssignmentsSummaryCard
        count={assignmentsCount}
        label="Assignments available"
        iconName="document-text-outline"
        compact
        style={styles.summaryCardHalf}
      />
      <AssignmentsSummaryCard
        count={notesCount}
        label="Notes available"
        iconName="notebook-outline"
        iconFamily="material"
        compact
        style={styles.summaryCardHalf}
      />
    </View>
  );
}

export function AssignmentListItem({
  item,
  onPress,
}: {
  item: any;
  onPress: () => void;
}) {
  const title = String(item.document_name ?? 'Assignment').trim() || 'Assignment';
  const batch = String(item.batch_name ?? item.batch ?? '').trim();
  const subject = String(item.subject_name ?? '').trim();
  const isLink = String(item.type ?? '').toLowerCase() === 'link';
  const fileCount = Number(item.file_count ?? 0);
  const fileLabel = isLink ? 'Link' : fileCount > 1 ? `${fileCount} files` : 'File';

  return (
    <Pressable style={({ pressed }) => [styles.listItem, pressed && styles.pressed]} onPress={onPress}>
      <View style={styles.listIcon}>
        <AppIcon
          name={isLink ? 'link-outline' : 'document-attach-outline'}
          family="ionicons"
          size={20}
          color={PRIMARY}
        />
      </View>
      <View style={styles.listBody}>
        <Text style={styles.listTitle} numberOfLines={1}>
          {title}
        </Text>
        <Text style={styles.listMeta} numberOfLines={1}>
          {[subject, batch, fileLabel].filter(Boolean).join(' · ')}
        </Text>
      </View>
      <View style={[styles.typeBadge, isLink ? styles.typeLink : styles.typeFile]}>
        <Text style={[styles.typeText, isLink ? styles.typeLinkText : styles.typeFileText]}>
          {fileLabel}
        </Text>
      </View>
      <AppIcon name="chevron-forward" size={18} color={theme.muted} />
    </Pressable>
  );
}

export function AssignmentHeroCard({
  title,
  type,
  batch,
}: {
  title?: string | null;
  type?: string | null;
  batch?: string | null;
}) {
  const name = String(title ?? 'Assignment').trim() || 'Assignment';
  const isLink = String(type ?? '').toLowerCase() === 'link';
  const batchText = String(batch ?? '').trim();

  return (
    <View style={styles.heroCard}>
      <View style={styles.heroIcon}>
        <AppIcon
          name={isLink ? 'link-outline' : 'document-text-outline'}
          family="ionicons"
          size={28}
          color={PRIMARY}
        />
      </View>
      <Text style={styles.heroTitle}>{name}</Text>
      <View style={styles.heroBadges}>
        <View style={[styles.typeBadge, isLink ? styles.typeLink : styles.typeFile]}>
          <Text style={[styles.typeText, isLink ? styles.typeLinkText : styles.typeFileText]}>
            {isLink ? 'Link assignment' : 'File assignment'}
          </Text>
        </View>
        {batchText ? (
          <View style={styles.batchBadge}>
            <Text style={styles.batchText}>{batchText}</Text>
          </View>
        ) : null}
      </View>
    </View>
  );
}

export function AssignmentDetailRow({
  label,
  value,
}: {
  label: string;
  value?: string | number | null;
}) {
  const text = value != null && String(value).trim() !== '' ? String(value) : '—';
  return (
    <View style={styles.detailRow}>
      <Text style={styles.detailLabel}>{label}</Text>
      <Text style={styles.detailValue} numberOfLines={3}>
        {text}
      </Text>
    </View>
  );
}

export function AssignmentFilesList({
  files,
  onOpen,
}: {
  files: { index: number; name: string }[];
  onOpen: (index: number) => void;
}) {
  if (!files.length) return null;

  return (
    <View style={styles.filesCard}>
      <Text style={styles.filesTitle}>Attached files ({files.length})</Text>
      {files.map((file, idx) => (
        <Pressable
          key={`${file.index}-${file.name}`}
          style={[styles.fileRow, idx === files.length - 1 && styles.fileRowLast]}
          onPress={() => onOpen(file.index)}>
          <AppIcon name="document-text-outline" family="ionicons" size={18} color={PRIMARY} />
          <Text style={styles.fileRowText} numberOfLines={2}>
            {file.name}
          </Text>
          <AppIcon name="chevron-forward" size={18} color={theme.muted} />
        </Pressable>
      ))}
    </View>
  );
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
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.06,
    shadowRadius: 14,
    elevation: 3,
  },
  summaryCardCompact: {
    padding: 14,
    gap: 10,
    borderRadius: 18,
  },
  summaryCardHalf: {
    flex: 1,
    marginBottom: 0,
  },
  summaryRow: {
    flexDirection: 'row',
    gap: 10,
    marginBottom: 14,
  },
  summaryIcon: {
    width: 50,
    height: 50,
    borderRadius: 16,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  summaryIconCompact: {
    width: 42,
    height: 42,
    borderRadius: 14,
  },
  summaryBody: { flex: 1 },
  summaryValue: { fontSize: 28, fontWeight: '800', color: theme.text },
  summaryValueCompact: { fontSize: 24 },
  summaryLabel: { fontSize: 13, color: theme.muted, marginTop: 4 },
  summaryLabelCompact: { fontSize: 11, lineHeight: 15 },
  listItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 14,
    gap: 10,
  },
  pressed: { opacity: 0.92 },
  listIcon: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
  },
  listBody: { flex: 1 },
  listTitle: { fontSize: 15, fontWeight: '800', color: theme.text },
  listMeta: { fontSize: 12, color: theme.muted, marginTop: 3 },
  typeBadge: {
    borderRadius: 999,
    paddingHorizontal: 10,
    paddingVertical: 5,
  },
  typeLink: { backgroundColor: '#e0f2fe' },
  typeFile: { backgroundColor: '#ede9fe' },
  typeText: { fontSize: 10, fontWeight: '800' },
  typeLinkText: { color: PRIMARY },
  typeFileText: { color: '#6d28d9' },
  heroCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 20,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'center',
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.08,
    shadowRadius: 18,
    elevation: 4,
  },
  heroIcon: {
    width: 64,
    height: 64,
    borderRadius: 20,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 14,
  },
  heroTitle: {
    fontSize: 22,
    fontWeight: '800',
    color: theme.text,
    textAlign: 'center',
    lineHeight: 28,
  },
  heroBadges: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    gap: 8,
    marginTop: 12,
  },
  batchBadge: {
    backgroundColor: theme.primaryMuted,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 6,
  },
  batchText: { fontSize: 11, fontWeight: '700', color: PRIMARY },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    gap: 16,
    paddingVertical: 12,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  detailLabel: { fontSize: 13, fontWeight: '600', color: theme.muted, flex: 1 },
  detailValue: {
    fontSize: 14,
    fontWeight: '700',
    color: theme.text,
    flex: 1.2,
    textAlign: 'right',
  },
  filesCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
  },
  filesTitle: { fontSize: 15, fontWeight: '800', color: theme.text, marginBottom: 10 },
  fileRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: theme.border,
  },
  fileRowLast: { borderBottomWidth: 0, paddingBottom: 0 },
  fileRowText: { flex: 1, fontSize: 14, color: theme.text, fontWeight: '600' },
});
