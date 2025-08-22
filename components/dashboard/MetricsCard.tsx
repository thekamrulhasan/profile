import type React from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Progress } from "@/components/ui/progress"
import { TrendingUp, TrendingDown, Minus } from "lucide-react"

interface MetricsCardProps {
  title: string
  value: string | number
  change?: number
  changeType?: "increase" | "decrease" | "neutral"
  description?: string
  progress?: number
  icon?: React.ReactNode
}

export default function MetricsCard({
  title,
  value,
  change,
  changeType = "neutral",
  description,
  progress,
  icon,
}: MetricsCardProps) {
  const getTrendIcon = () => {
    switch (changeType) {
      case "increase":
        return <TrendingUp className="h-4 w-4 text-green-600" />
      case "decrease":
        return <TrendingDown className="h-4 w-4 text-red-600" />
      default:
        return <Minus className="h-4 w-4 text-gray-400" />
    }
  }

  const getTrendColor = () => {
    switch (changeType) {
      case "increase":
        return "text-green-600"
      case "decrease":
        return "text-red-600"
      default:
        return "text-gray-400"
    }
  }

  return (
    <Card className="hover:shadow-lg transition-shadow duration-200">
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium text-gray-600">{title}</CardTitle>
        {icon && <div className="text-gray-400">{icon}</div>}
      </CardHeader>
      <CardContent>
        <div className="flex items-center justify-between">
          <div className="text-2xl font-bold text-gray-900">
            {typeof value === "number" ? value.toLocaleString() : value}
          </div>
          {change !== undefined && (
            <div className={`flex items-center space-x-1 ${getTrendColor()}`}>
              {getTrendIcon()}
              <span className="text-sm font-medium">{Math.abs(change)}%</span>
            </div>
          )}
        </div>
        {description && <p className="text-xs text-gray-500 mt-1">{description}</p>}
        {progress !== undefined && (
          <div className="mt-3">
            <Progress value={progress} className="h-2" />
            <p className="text-xs text-gray-500 mt-1">{progress}% of target</p>
          </div>
        )}
      </CardContent>
    </Card>
  )
}
