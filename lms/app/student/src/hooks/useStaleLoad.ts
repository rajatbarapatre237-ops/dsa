import { useCallback, useRef, useState } from 'react';

type LoadOptions = {
  showRefresh?: boolean;
};

export function useStaleLoad() {
  const hasDataRef = useRef(false);
  const inFlightRef = useRef(0);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const beginLoad = useCallback((options?: LoadOptions) => {
    inFlightRef.current += 1;
    if (!hasDataRef.current) {
      setLoading(true);
    } else if (options?.showRefresh) {
      setRefreshing(true);
    }
  }, []);

  const endLoad = useCallback(() => {
    inFlightRef.current = Math.max(0, inFlightRef.current - 1);
    if (inFlightRef.current === 0) {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  const markHasData = useCallback(() => {
    hasDataRef.current = true;
  }, []);

  return { loading, refreshing, beginLoad, endLoad, markHasData };
}
