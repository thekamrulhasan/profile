"use client"

import { useEffect, useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Activity, Users, Eye, TrendingUp } from "lucide-react"

interface RealTimeData {
  active_visitors: number
  recent_pageviews: Array<{ date: string; pageviews: number }>
  top_pages: Record<string, number>
}

export default function RealTimeAnalytics() {
  const [data, setData] = useState<RealTimeData | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    const fetchRealTimeData = async () => {
      try {
        const response = await fetch("/api/v1/analytics/realtime", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
        })
        if (response.ok) {
          const result = await response.json()
          setData(result)
        }
      } catch (error) {
        console.error("Failed to fetch real-time analytics:", error)
      } finally {
        setIsLoading(false)
      }
    }

    // Initial fetch
    fetchRealTimeData()

    // Update every 30 seconds
    const interval = setInterval(fetchRealTimeData, 30000)

    return () => clearInterval(interval)
  }, [])

  if (isLoading) {
    return (
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Activity className="h-5 w-5 text-green-500 animate-pulse" />
            Real-Time Analytics
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="animate-pulse space-y-4">
            <div className="h-8 bg-gray-200 rounded"></div>
            <div className="h-4 bg-gray-200 rounded w-3/4"></div>
            <div className="h-4 bg-gray-200 rounded w-1/2"></div>
          </div>
        </CardContent>
      </Card>
    )
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <Activity className="h-5 w-5 text-green-500" />
          Real-Time Analytics
          <Badge variant="secondary" className="ml-auto">
            Live
          </Badge>
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-6">
        {/* Active Visitors */}
        <div className="flex items-center justify-between p-4 bg-green-50 rounded-lg">
          <div className="flex items-center gap-3">
            <Users className="h-8 w-8 text-green-600" />
            <div>
              <p className="text-sm text-green-600 font-medium">Active Visitors</p>
              <p className="text-2xl font-bold text-green-800">{data?.active_visitors || 0}</p>
            </div>
          </div>
          <div className="h-3 w-3 bg-green-500 rounded-full animate-pulse"></div>
        </div>

        {/* Recent Activity */}
        <div>
          <h4 className="font-semibold mb-3 flex items-center gap-2">
            <TrendingUp className="h-4 w-4" />
            Recent Activity (5 min)
          </h4>
          <div className="space-y-2">
            {data?.recent_pageviews?.slice(0, 3).map((item, index) => (
              <div key={index} className="flex items-center justify-between text-sm">
                <span className="text-gray-600">{item.date}</span>
                <Badge variant="outline">{item.pageviews} views</Badge>
              </div>
            ))}
          </div>
        </div>

        {/* Top Pages */}
        <div>
          <h4 className="font-semibold mb-3 flex items-center gap-2">
            <Eye className="h-4 w-4" />
            Top Pages Right Now
          </h4>
          <div className="space-y-2">
            {Object.entries(data?.top_pages || {})
              .slice(0, 5)
              .map(([page, views]) => (
                <div key={page} className="flex items-center justify-between text-sm">
                  <span className="text-gray-600 truncate flex-1" title={page}>
                    {page.replace(/^https?:\/\/[^/]+/, "") || "/"}
                  </span>
                  <Badge variant="secondary">{views}</Badge>
                </div>
              ))}
          </div>
        </div>
      </CardContent>
    </Card>
  )
}
