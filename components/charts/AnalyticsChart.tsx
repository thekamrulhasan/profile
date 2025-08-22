"use client"

import { useEffect, useRef } from "react"
import { Chart, type ChartConfiguration } from "chart.js/auto"

interface AnalyticsChartProps {
  type: "line" | "bar" | "doughnut" | "pie"
  data: any
  options?: any
  className?: string
}

export default function AnalyticsChart({ type, data, options = {}, className = "w-full h-64" }: AnalyticsChartProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null)
  const chartRef = useRef<Chart | null>(null)

  useEffect(() => {
    if (!canvasRef.current) return

    // Destroy existing chart
    if (chartRef.current) {
      chartRef.current.destroy()
    }

    const ctx = canvasRef.current.getContext("2d")
    if (!ctx) return

    const config: ChartConfiguration = {
      type,
      data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top" as const,
          },
          tooltip: {
            mode: "index",
            intersect: false,
          },
        },
        scales:
          type !== "doughnut" && type !== "pie"
            ? {
                x: {
                  display: true,
                  grid: {
                    color: "rgba(0, 0, 0, 0.1)",
                  },
                },
                y: {
                  display: true,
                  beginAtZero: true,
                  grid: {
                    color: "rgba(0, 0, 0, 0.1)",
                  },
                },
              }
            : undefined,
        ...options,
      },
    }

    chartRef.current = new Chart(ctx, config)

    return () => {
      if (chartRef.current) {
        chartRef.current.destroy()
      }
    }
  }, [type, data, options])

  return (
    <div className={className}>
      <canvas ref={canvasRef} />
    </div>
  )
}
