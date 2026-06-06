import React, { useMemo, useState } from 'react';
import { View, Text, Pressable, Modal, StyleSheet } from 'react-native';
import AppIcon from './AppIcon';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

const WEEKDAYS = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
const CELL_SIZE = 40;

function toISODate(date: Date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

function parseISODate(value: string) {
  const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value);
  if (!match) {
    return new Date();
  }
  return new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
}

function formatDisplayDate(value: string, withWeekday = false) {
  const date = parseISODate(value);
  return date.toLocaleDateString('en-IN', {
    weekday: withWeekday ? 'short' : undefined,
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
}

export function FormDateInput({
  label,
  value,
  onChange,
}: {
  label: string;
  value: string;
  onChange: (isoDate: string) => void;
}) {
  const selected = parseISODate(value);
  const todayIso = toISODate(new Date());
  const [open, setOpen] = useState(false);
  const [viewYear, setViewYear] = useState(selected.getFullYear());
  const [viewMonth, setViewMonth] = useState(selected.getMonth());
  const [draft, setDraft] = useState(value);

  const monthLabel = useMemo(
    () =>
      new Date(viewYear, viewMonth, 1).toLocaleDateString('en-IN', {
        month: 'long',
        year: 'numeric',
      }),
    [viewMonth, viewYear],
  );

  const calendarCells = useMemo(() => {
    const firstDay = new Date(viewYear, viewMonth, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
    const cells: Array<{ key: string; day: number | null }> = [];

    for (let i = 0; i < firstDay; i += 1) {
      cells.push({ key: `empty-${i}`, day: null });
    }
    for (let day = 1; day <= daysInMonth; day += 1) {
      cells.push({ key: `day-${day}`, day });
    }
    return cells;
  }, [viewMonth, viewYear]);

  function openPicker() {
    const date = parseISODate(value);
    setViewYear(date.getFullYear());
    setViewMonth(date.getMonth());
    setDraft(value);
    setOpen(true);
  }

  function closePicker() {
    setOpen(false);
  }

  function shiftMonth(delta: number) {
    const next = new Date(viewYear, viewMonth + delta, 1);
    setViewYear(next.getFullYear());
    setViewMonth(next.getMonth());
  }

  function goToToday() {
    const now = new Date();
    setViewYear(now.getFullYear());
    setViewMonth(now.getMonth());
    setDraft(todayIso);
  }

  function applyDate() {
    onChange(draft);
    closePicker();
  }

  return (
    <View style={styles.wrap}>
      <Text style={styles.label}>{label}</Text>
      <Pressable style={styles.field} onPress={openPicker}>
        <Text style={styles.value} numberOfLines={1}>
          {formatDisplayDate(value)}
        </Text>
        <View style={styles.iconWrap}>
          <AppIcon name="calendar-outline" size={18} color={PRIMARY} />
        </View>
      </Pressable>

      <Modal visible={open} transparent animationType="fade" onRequestClose={closePicker}>
        <View style={styles.overlay}>
          <Pressable style={styles.backdrop} onPress={closePicker} />
          <View style={styles.sheet}>
            <View style={styles.sheetHeader}>
              <View style={styles.sheetHeaderText}>
                <Text style={styles.sheetTitle}>Select date</Text>
                <Text style={styles.sheetSubtitle}>{formatDisplayDate(draft, true)}</Text>
              </View>
              <Pressable style={styles.closeBtn} onPress={closePicker} hitSlop={8}>
                <AppIcon name="close" size={18} color={theme.muted} />
              </Pressable>
            </View>

            <View style={styles.monthBar}>
              <Pressable style={styles.navBtn} onPress={() => shiftMonth(-1)} hitSlop={8}>
                <AppIcon name="chevron-back" size={18} color={PRIMARY} />
              </Pressable>
              <Text style={styles.monthLabel}>{monthLabel}</Text>
              <Pressable style={styles.navBtn} onPress={() => shiftMonth(1)} hitSlop={8}>
                <AppIcon name="chevron-forward" size={18} color={PRIMARY} />
              </Pressable>
            </View>

            <View style={styles.weekdayRow}>
              {WEEKDAYS.map((day, index) => (
                <View key={`${day}-${index}`} style={styles.weekdayCell}>
                  <Text style={styles.weekday}>{day}</Text>
                </View>
              ))}
            </View>

            <View style={styles.grid}>
              {calendarCells.map(cell => {
                if (cell.day == null) {
                  return <View key={cell.key} style={styles.dayCell} />;
                }

                const iso = toISODate(new Date(viewYear, viewMonth, cell.day));
                const isSelected = iso === draft;
                const isToday = iso === todayIso;

                return (
                  <Pressable
                    key={cell.key}
                    style={styles.dayCell}
                    onPress={() => setDraft(iso)}>
                    <View style={[styles.dayInner, isSelected && styles.daySelected]}>
                      <Text
                        style={[
                          styles.dayText,
                          isSelected && styles.dayTextSelected,
                          isToday && !isSelected && styles.dayTextToday,
                        ]}>
                        {cell.day}
                      </Text>
                      {isToday && !isSelected ? <View style={styles.todayDot} /> : null}
                    </View>
                  </Pressable>
                );
              })}
            </View>

            <Pressable style={styles.todayBtn} onPress={goToToday}>
              <Text style={styles.todayBtnText}>Go to today</Text>
            </Pressable>

            <View style={styles.actions}>
              <Pressable style={styles.cancelBtn} onPress={closePicker}>
                <Text style={styles.cancelText}>Cancel</Text>
              </Pressable>
              <Pressable style={styles.doneBtn} onPress={applyDate}>
                <Text style={styles.doneText}>Done</Text>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { marginBottom: 14 },
  label: { fontSize: 13, fontWeight: '600', color: theme.text, marginBottom: 6 },
  field: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    borderRadius: 10,
    paddingLeft: 14,
    paddingRight: 10,
    paddingVertical: 12,
    backgroundColor: '#fff',
    gap: 8,
  },
  value: { flex: 1, fontSize: 16, color: theme.text },
  iconWrap: {
    width: 32,
    height: 32,
    borderRadius: 8,
    backgroundColor: '#eff6ff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  overlay: {
    flex: 1,
    justifyContent: 'center',
    paddingHorizontal: 20,
  },
  backdrop: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(15, 23, 42, 0.45)',
  },
  sheet: {
    backgroundColor: '#fff',
    borderRadius: 16,
    paddingHorizontal: 16,
    paddingTop: 16,
    paddingBottom: 14,
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.15,
    shadowRadius: 20,
    elevation: 12,
  },
  sheetHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
    marginBottom: 14,
  },
  sheetHeaderText: { flex: 1, paddingRight: 8 },
  sheetTitle: { fontSize: 17, fontWeight: '700', color: theme.text },
  sheetSubtitle: { fontSize: 14, color: PRIMARY, fontWeight: '600', marginTop: 4 },
  closeBtn: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#f8fafc',
  },
  monthBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#f8fafc',
    borderRadius: 10,
    paddingHorizontal: 8,
    paddingVertical: 8,
    marginBottom: 12,
  },
  navBtn: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#fff',
  },
  monthLabel: { fontSize: 15, fontWeight: '700', color: theme.text },
  weekdayRow: {
    flexDirection: 'row',
    marginBottom: 4,
  },
  weekdayCell: {
    width: `${100 / 7}%`,
    alignItems: 'center',
  },
  weekday: {
    fontSize: 11,
    fontWeight: '700',
    color: theme.muted,
  },
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    marginBottom: 10,
  },
  dayCell: {
    width: `${100 / 7}%`,
    height: CELL_SIZE,
    alignItems: 'center',
    justifyContent: 'center',
  },
  dayInner: {
    width: 34,
    height: 34,
    borderRadius: 17,
    alignItems: 'center',
    justifyContent: 'center',
  },
  daySelected: {
    backgroundColor: PRIMARY,
  },
  dayText: {
    fontSize: 14,
    color: theme.text,
    fontWeight: '600',
  },
  dayTextSelected: {
    color: '#fff',
  },
  dayTextToday: {
    color: PRIMARY,
    fontWeight: '700',
  },
  todayDot: {
    position: 'absolute',
    bottom: 4,
    width: 4,
    height: 4,
    borderRadius: 2,
    backgroundColor: PRIMARY,
  },
  todayBtn: {
    alignSelf: 'center',
    paddingVertical: 6,
    paddingHorizontal: 12,
    marginBottom: 12,
  },
  todayBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: PRIMARY,
  },
  actions: {
    flexDirection: 'row',
    gap: 10,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: '#e2e8f0',
    paddingTop: 12,
  },
  cancelBtn: {
    flex: 1,
    paddingVertical: 11,
    borderRadius: 10,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#cbd5e1',
    backgroundColor: '#fff',
  },
  cancelText: {
    color: theme.text,
    fontWeight: '700',
    fontSize: 15,
  },
  doneBtn: {
    flex: 1,
    paddingVertical: 11,
    borderRadius: 10,
    alignItems: 'center',
    backgroundColor: PRIMARY,
  },
  doneText: {
    color: '#fff',
    fontWeight: '700',
    fontSize: 15,
  },
});
