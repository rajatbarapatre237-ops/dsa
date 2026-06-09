import React, { useRef, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Pressable,
  FlatList,
  Dimensions,
  NativeScrollEvent,
  NativeSyntheticEvent,
} from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

const SCREEN_WIDTH = Dimensions.get('window').width;
const SCREEN_HORIZONTAL_PADDING = 16;
const MARKS_PAGE_WIDTH = SCREEN_WIDTH - SCREEN_HORIZONTAL_PADDING * 2;

export type TestResult = {
  test_name?: string | null;
  test_date?: string | null;
  marks_obtained?: number | string | null;
  total_marks?: number | string | null;
  passing_marks?: number | string | null;
  subject_name?: string | null;
};

export function isTestPassed(result: TestResult) {
  const obtained = Number(result.marks_obtained);
  const passing = Number(result.passing_marks);
  if (Number.isNaN(obtained) || Number.isNaN(passing)) return null;
  return obtained >= passing;
}

export function marksStats(results: TestResult[]) {
  const total = results.length;
  const passed = results.filter(r => isTestPassed(r) === true).length;
  const failed = results.filter(r => isTestPassed(r) === false).length;
  const scored = results.filter(r => {
    const obtained = Number(r.marks_obtained);
    const max = Number(r.total_marks);
    return !Number.isNaN(obtained) && !Number.isNaN(max) && max > 0;
  });
  const averagePercent = scored.length
    ? Math.round(
        scored.reduce((sum, r) => sum + (Number(r.marks_obtained) / Number(r.total_marks)) * 100, 0) /
          scored.length,
      )
    : 0;
  const passRate = total ? Math.round((passed / total) * 100) : 0;
  return { total, passed, failed, averagePercent, passRate };
}

function resultTone(result: TestResult) {
  const passed = isTestPassed(result);
  if (passed === true) {
    return { label: 'Pass', bg: '#dcfce7', color: '#166534' };
  }
  if (passed === false) {
    return { label: 'Fail', bg: '#fee2e2', color: '#991b1b' };
  }
  return { label: 'Scored', bg: '#e0f2fe', color: PRIMARY };
}

function formatShortDate(value?: string | null) {
  if (!value) return '—';
  const date = new Date(`${value}T12:00:00`);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
}

export function LatestMarksCard({
  result,
  onPress,
}: {
  result?: TestResult | null;
  onPress?: () => void;
}) {
  if (!result) {
    return (
      <View style={styles.latestCard}>
        <View style={styles.latestEmptyIcon}>
          <AppIcon name="school-outline" size={28} color={PRIMARY} />
        </View>
        <Text style={styles.latestEmptyTitle}>No test results yet</Text>
        <Text style={styles.latestEmptySub}>Results will appear here once class tests are scored</Text>
      </View>
    );
  }

  const tone = resultTone(result);
  const obtained = result.marks_obtained ?? '—';
  const total = result.total_marks ?? '—';
  const content = (
    <View style={styles.latestCard}>
      <View style={styles.latestHeader}>
        <View style={styles.latestText}>
          <Text style={styles.latestEyebrow}>Latest result</Text>
          <Text style={styles.latestTitle} numberOfLines={2}>
            {result.test_name ?? 'Class test'}
          </Text>
          <Text style={styles.latestMeta}>
            {[formatShortDate(result.test_date), result.subject_name].filter(Boolean).join(' · ')}
          </Text>
        </View>
        <View style={styles.scoreBubble}>
          <Text style={styles.scoreValue}>
            {obtained}
            <Text style={styles.scoreMax}>/{total}</Text>
          </Text>
          <Text style={styles.scoreLabel}>Marks</Text>
        </View>
      </View>
      <View style={[styles.resultBadge, { backgroundColor: tone.bg }]}>
        <AppIcon
          name={tone.label === 'Pass' ? 'checkmark-circle' : tone.label === 'Fail' ? 'close-circle' : 'help-circle'}
          size={14}
          color={tone.color}
        />
        <Text style={[styles.resultBadgeText, { color: tone.color }]}>{tone.label}</Text>
      </View>
      {onPress ? (
        <View style={styles.viewDetailsRow}>
          <Text style={styles.viewDetailsText}>View details</Text>
          <AppIcon name="chevron-forward" size={16} color={PRIMARY} />
        </View>
      ) : null}
    </View>
  );

  if (onPress) {
    return (
      <Pressable onPress={onPress} style={({ pressed }) => pressed && styles.pressedCard}>
        {content}
      </Pressable>
    );
  }
  return content;
}

export function MarksOverviewCard({
  total,
  passed,
  failed,
  averagePercent,
  passRate,
}: {
  total: number;
  passed: number;
  failed: number;
  averagePercent: number;
  passRate: number;
}) {
  return (
    <View style={styles.overviewCard}>
      <View style={styles.overviewHeader}>
        <View>
          <Text style={styles.overviewEyebrow}>Performance</Text>
          <Text style={styles.overviewTitle}>Marks overview</Text>
        </View>
        <View style={styles.avgBubble}>
          <Text style={styles.avgValue}>{averagePercent}%</Text>
          <Text style={styles.avgLabel}>Avg score</Text>
        </View>
      </View>
      <View style={styles.progressTrack}>
        <View style={[styles.progressFill, { width: `${Math.min(100, passRate)}%` }]} />
      </View>
      <Text style={styles.passRateText}>{passRate}% tests passed</Text>
      <View style={styles.overviewStats}>
        <View style={[styles.overviewStat, styles.statPass]}>
          <Text style={[styles.overviewStatValue, { color: theme.success }]}>{passed}</Text>
          <Text style={styles.overviewStatLabel}>Passed</Text>
        </View>
        <View style={[styles.overviewStat, styles.statFail]}>
          <Text style={[styles.overviewStatValue, { color: theme.danger }]}>{failed}</Text>
          <Text style={styles.overviewStatLabel}>Failed</Text>
        </View>
        <View style={styles.overviewStat}>
          <Text style={styles.overviewStatValue}>{total}</Text>
          <Text style={styles.overviewStatLabel}>Total tests</Text>
        </View>
      </View>
    </View>
  );
}

function MarksSlideCard({
  result,
  onPress,
}: {
  result: TestResult;
  onPress?: () => void;
}) {
  const tone = resultTone(result);
  const obtained = result.marks_obtained ?? '—';
  const total = result.total_marks ?? '—';
  const percent =
    Number(result.marks_obtained) && Number(result.total_marks)
      ? Math.round((Number(result.marks_obtained) / Number(result.total_marks)) * 100)
      : null;

  const content = (
    <View style={styles.slideCard}>
      <View style={styles.slideTop}>
        <View style={styles.slideText}>
          <Text style={styles.slideEyebrow}>Class test</Text>
          <Text style={styles.slideTitle} numberOfLines={2}>
            {result.test_name ?? 'Test'}
          </Text>
          <Text style={styles.slideMeta} numberOfLines={1}>
            {[formatShortDate(result.test_date), result.subject_name].filter(Boolean).join(' · ')}
          </Text>
        </View>
        <View style={styles.slideScoreBubble}>
          <Text style={styles.slideScoreValue}>
            {obtained}
            <Text style={styles.slideScoreMax}>/{total}</Text>
          </Text>
          {percent != null ? <Text style={styles.slidePercent}>{percent}%</Text> : null}
        </View>
      </View>
      <View style={styles.slideFooter}>
        <View style={[styles.resultBadge, { backgroundColor: tone.bg }]}>
          <AppIcon
            name={tone.label === 'Pass' ? 'checkmark-circle' : tone.label === 'Fail' ? 'close-circle' : 'help-circle'}
            size={14}
            color={tone.color}
          />
          <Text style={[styles.resultBadgeText, { color: tone.color }]}>{tone.label}</Text>
        </View>
        {onPress ? (
          <View style={styles.slideTapHint}>
            <Text style={styles.slideTapText}>Details</Text>
            <AppIcon name="chevron-forward" size={14} color={PRIMARY} />
          </View>
        ) : null}
      </View>
    </View>
  );

  if (onPress) {
    return (
      <Pressable onPress={onPress} style={({ pressed }) => pressed && styles.pressedCard}>
        {content}
      </Pressable>
    );
  }
  return content;
}

export function RecentMarksCarousel({
  results,
  onItemPress,
}: {
  results: TestResult[];
  onItemPress?: (result: TestResult) => void;
}) {
  const listRef = useRef<FlatList<TestResult>>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  if (!results.length) {
    return (
      <View style={styles.carouselEmpty}>
        <View style={styles.latestEmptyIcon}>
          <AppIcon name="school-outline" size={24} color={PRIMARY} />
        </View>
        <Text style={styles.carouselEmptyTitle}>No test marks yet</Text>
        <Text style={styles.carouselEmptySub}>Recent class test scores will appear here</Text>
      </View>
    );
  }

  function updateActiveIndex(offset: number) {
    const index = Math.round(offset / MARKS_PAGE_WIDTH);
    if (index >= 0 && index < results.length && index !== activeIndex) {
      setActiveIndex(index);
    }
  }

  function onScroll(event: NativeSyntheticEvent<NativeScrollEvent>) {
    updateActiveIndex(event.nativeEvent.contentOffset.x);
  }

  return (
    <View style={styles.carouselWrap}>
      <FlatList
        ref={listRef}
        horizontal
        nestedScrollEnabled
        pagingEnabled
        data={results}
        keyExtractor={(item, index) => `${item.test_name}-${item.test_date}-${index}`}
        showsHorizontalScrollIndicator={false}
        decelerationRate="fast"
        snapToInterval={MARKS_PAGE_WIDTH}
        snapToAlignment="start"
        disableIntervalMomentum
        getItemLayout={(_, index) => ({
          length: MARKS_PAGE_WIDTH,
          offset: MARKS_PAGE_WIDTH * index,
          index,
        })}
        onScroll={onScroll}
        onMomentumScrollEnd={event => updateActiveIndex(event.nativeEvent.contentOffset.x)}
        scrollEventThrottle={16}
        renderItem={({ item }) => (
          <View style={styles.carouselItem}>
            <MarksSlideCard result={item} onPress={onItemPress ? () => onItemPress(item) : undefined} />
          </View>
        )}
      />
      {results.length > 1 ? (
        <View style={styles.carouselDots}>
          {results.map((_, index) => (
            <View
              key={index}
              style={[styles.carouselDot, index === activeIndex && styles.carouselDotActive]}
            />
          ))}
        </View>
      ) : null}
    </View>
  );
}

export function ExploreMarksTiles({
  onAllMarks,
  onClassResults,
}: {
  onAllMarks: () => void;
  onClassResults: () => void;
}) {
  return (
    <View style={styles.exploreRow}>
      <Pressable
        style={({ pressed }) => [styles.exploreTile, styles.exploreTileAll, pressed && styles.pressedTile]}
        onPress={onAllMarks}>
        <View style={[styles.exploreIcon, { backgroundColor: '#e0f2fe' }]}>
          <AppIcon name="format-list-bulleted" family="material" size={22} color={PRIMARY} />
        </View>
        <Text style={styles.exploreTitle}>All marks</Text>
        <Text style={styles.exploreSub}>Every test score</Text>
      </Pressable>
      <Pressable
        style={({ pressed }) => [styles.exploreTile, styles.exploreTileResults, pressed && styles.pressedTile]}
        onPress={onClassResults}>
        <View style={[styles.exploreIcon, { backgroundColor: '#ede9fe' }]}>
          <AppIcon name="clipboard-text-outline" family="material" size={22} color={PRIMARY} />
        </View>
        <Text style={styles.exploreTitle}>Results</Text>
        <Text style={styles.exploreSub}>Class test details</Text>
      </Pressable>
    </View>
  );
}

export function RecentMarksCard({
  results,
  onViewAll,
  onItemPress,
  showHeader = true,
}: {
  results: TestResult[];
  onViewAll?: () => void;
  onItemPress?: (result: TestResult) => void;
  showHeader?: boolean;
}) {
  return (
    <View style={styles.recentCard}>
      {showHeader ? (
        <View style={styles.recentHeader}>
          <Text style={styles.recentTitle}>Recent tests</Text>
          {onViewAll ? (
            <Pressable onPress={onViewAll} hitSlop={8}>
              <Text style={styles.recentLink}>See all</Text>
            </Pressable>
          ) : null}
        </View>
      ) : null}
      {results.length > 0 ? (
        results.map((result, index) => {
          const tone = resultTone(result);
          const row = (
            <View style={[styles.recentRow, index > 0 && styles.recentRowBorder]}>
              <View style={styles.recentMain}>
                <Text style={styles.recentName} numberOfLines={1}>
                  {result.test_name ?? 'Class test'}
                </Text>
                <Text style={styles.recentMeta}>
                  {formatShortDate(result.test_date)}
                  {result.subject_name ? ` · ${result.subject_name}` : ''}
                </Text>
              </View>
              <View style={styles.recentRight}>
                <Text style={styles.recentScore}>
                  {result.marks_obtained ?? '—'}/{result.total_marks ?? '—'}
                </Text>
                <View style={[styles.recentBadge, { backgroundColor: tone.bg }]}>
                  <Text style={[styles.recentBadgeText, { color: tone.color }]}>{tone.label}</Text>
                </View>
              </View>
              {onItemPress ? <AppIcon name="chevron-forward" size={18} color={theme.muted} /> : null}
            </View>
          );

          if (onItemPress) {
            return (
              <Pressable key={`${result.test_name}-${result.test_date}-${index}`} onPress={() => onItemPress(result)}>
                {row}
              </Pressable>
            );
          }
          return <View key={`${result.test_name}-${result.test_date}-${index}`}>{row}</View>;
        })
      ) : (
        <Text style={styles.recentEmpty}>No scored tests to show yet</Text>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  latestCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.08,
    shadowRadius: 18,
    elevation: 4,
  },
  latestHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    gap: 12,
  },
  latestText: { flex: 1 },
  latestEyebrow: {
    fontSize: 11,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.6,
  },
  latestTitle: { fontSize: 18, fontWeight: '800', color: theme.text, marginTop: 4, lineHeight: 24 },
  latestMeta: { fontSize: 12, color: theme.muted, marginTop: 6 },
  scoreBubble: {
    alignItems: 'center',
    backgroundColor: theme.primarySoft,
    borderRadius: 16,
    paddingHorizontal: 14,
    paddingVertical: 12,
    minWidth: 84,
  },
  scoreValue: { fontSize: 24, fontWeight: '800', color: PRIMARY },
  scoreMax: { fontSize: 14, fontWeight: '700', color: theme.muted },
  scoreLabel: { fontSize: 10, fontWeight: '700', color: theme.muted, marginTop: 2 },
  resultBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    gap: 6,
    borderRadius: 999,
    paddingHorizontal: 12,
    paddingVertical: 7,
    marginTop: 14,
  },
  resultBadgeText: { fontSize: 12, fontWeight: '800' },
  latestEmptyIcon: {
    width: 56,
    height: 56,
    borderRadius: 18,
    backgroundColor: theme.primarySoft,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  latestEmptyTitle: { fontSize: 16, fontWeight: '800', color: theme.text },
  latestEmptySub: { fontSize: 13, color: theme.muted, marginTop: 6, lineHeight: 18 },
  viewDetailsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    marginTop: 14,
    paddingTop: 12,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
  },
  viewDetailsText: { fontSize: 13, fontWeight: '700', color: PRIMARY },
  pressedCard: { opacity: 0.94, transform: [{ scale: 0.995 }] },
  overviewCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.06,
    shadowRadius: 14,
    elevation: 3,
  },
  overviewHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  overviewEyebrow: {
    fontSize: 11,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.6,
  },
  overviewTitle: { fontSize: 18, fontWeight: '800', color: theme.text, marginTop: 4 },
  avgBubble: {
    alignItems: 'center',
    backgroundColor: '#ede9fe',
    borderRadius: 14,
    paddingHorizontal: 14,
    paddingVertical: 10,
    minWidth: 72,
  },
  avgValue: { fontSize: 22, fontWeight: '800', color: '#6d28d9' },
  avgLabel: { fontSize: 10, fontWeight: '700', color: '#6d28d9', marginTop: 2 },
  progressTrack: {
    height: 8,
    backgroundColor: '#f1f5f9',
    borderRadius: 999,
    overflow: 'hidden',
    marginBottom: 8,
  },
  progressFill: {
    height: '100%',
    backgroundColor: theme.success,
    borderRadius: 999,
  },
  passRateText: {
    fontSize: 12,
    fontWeight: '600',
    color: theme.muted,
    marginBottom: 14,
  },
  overviewStats: { flexDirection: 'row', gap: 8 },
  overviewStat: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 14,
    paddingVertical: 12,
    alignItems: 'center',
  },
  statPass: { backgroundColor: '#ecfdf3' },
  statFail: { backgroundColor: '#fef2f2' },
  overviewStatValue: { fontSize: 20, fontWeight: '800', color: theme.text },
  overviewStatLabel: { fontSize: 10, fontWeight: '700', color: theme.muted, marginTop: 4 },
  exploreRow: { flexDirection: 'row', gap: 12, marginBottom: 8 },
  exploreTile: {
    flex: 1,
    backgroundColor: theme.card,
    borderRadius: 18,
    padding: 16,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'flex-start',
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 2,
  },
  exploreTileAll: { borderColor: '#bae6fd' },
  exploreTileResults: { borderColor: '#ddd6fe' },
  pressedTile: { opacity: 0.92, transform: [{ scale: 0.98 }] },
  exploreIcon: {
    width: 44,
    height: 44,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  exploreTitle: { fontSize: 15, fontWeight: '800', color: theme.text },
  exploreSub: { fontSize: 12, color: theme.muted, marginTop: 4 },
  recentCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
  },
  recentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  recentTitle: { fontSize: 16, fontWeight: '800', color: theme.text },
  recentLink: { fontSize: 13, fontWeight: '700', color: PRIMARY },
  recentRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    gap: 10,
  },
  recentRowBorder: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
  },
  recentMain: { flex: 1 },
  recentName: { fontSize: 14, fontWeight: '800', color: theme.text },
  recentMeta: { fontSize: 11, color: theme.muted, marginTop: 3 },
  recentRight: { alignItems: 'flex-end' },
  recentScore: { fontSize: 13, fontWeight: '800', color: theme.text },
  recentBadge: {
    borderRadius: 999,
    paddingHorizontal: 8,
    paddingVertical: 4,
    marginTop: 4,
  },
  recentBadgeText: { fontSize: 10, fontWeight: '800' },
  recentEmpty: { fontSize: 14, color: theme.muted, textAlign: 'center', paddingVertical: 12 },
  carouselWrap: { marginBottom: 14 },
  carouselItem: {
    width: MARKS_PAGE_WIDTH,
  },
  slideCard: {
    width: '100%',
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 18,
    borderWidth: 1,
    borderColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.07,
    shadowRadius: 16,
    elevation: 3,
    minHeight: 148,
  },
  slideTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    gap: 12,
  },
  slideText: { flex: 1 },
  slideEyebrow: {
    fontSize: 10,
    fontWeight: '700',
    color: PRIMARY,
    textTransform: 'uppercase',
    letterSpacing: 0.6,
  },
  slideTitle: { fontSize: 17, fontWeight: '800', color: theme.text, marginTop: 4, lineHeight: 22 },
  slideMeta: { fontSize: 12, color: theme.muted, marginTop: 6 },
  slideScoreBubble: {
    alignItems: 'center',
    backgroundColor: theme.primarySoft,
    borderRadius: 16,
    paddingHorizontal: 12,
    paddingVertical: 10,
    minWidth: 76,
  },
  slideScoreValue: { fontSize: 22, fontWeight: '800', color: PRIMARY },
  slideScoreMax: { fontSize: 13, fontWeight: '700', color: theme.muted },
  slidePercent: { fontSize: 11, fontWeight: '700', color: theme.muted, marginTop: 2 },
  slideFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 14,
  },
  slideTapHint: { flexDirection: 'row', alignItems: 'center', gap: 2 },
  slideTapText: { fontSize: 12, fontWeight: '700', color: PRIMARY },
  carouselDots: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 8,
    marginTop: 14,
    paddingBottom: 2,
  },
  carouselDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#cbd5e1',
  },
  carouselDotActive: {
    width: 24,
    backgroundColor: PRIMARY,
  },
  carouselEmpty: {
    backgroundColor: theme.card,
    borderRadius: 20,
    padding: 20,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    alignItems: 'center',
  },
  carouselEmptyTitle: { fontSize: 15, fontWeight: '800', color: theme.text, marginTop: 4 },
  carouselEmptySub: { fontSize: 12, color: theme.muted, marginTop: 4, textAlign: 'center' },
});
