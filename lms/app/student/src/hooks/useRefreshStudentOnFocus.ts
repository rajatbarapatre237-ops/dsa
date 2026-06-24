import { useCallback, useRef } from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { useStudentContext } from './useStudentContext';

export function useRefreshStudentOnFocus() {
  const { refresh } = useStudentContext();
  const refreshRef = useRef(refresh);
  refreshRef.current = refresh;

  useFocusEffect(
    useCallback(() => {
      refreshRef.current({ showRefresh: false });
    }, []),
  );
}
