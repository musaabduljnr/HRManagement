<?php

namespace App\Services;

use App\HrPolicy;

class HrAssistantService
{
    public function ask($question)
    {
        $questionLower = strtolower($question);
        
        $policies = HrPolicy::all();
        
        foreach ($policies as $policy) {
            $keywords = explode(' ', strtolower($policy->title));
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 3 && strpos($questionLower, $keyword) !== false) {
                    return [
                        'answer' => $policy->content,
                        'policy' => $policy
                    ];
                }
            }
        }

        if (strpos($questionLower, 'leave') !== false || strpos($questionLower, 'vacation') !== false) {
            return [
                'answer' => "Standard Leave Policy: Employees are entitled to 20 days of paid annual leave. Requests must be submitted at least 2 weeks in advance via the Leave module and approved by your Department Manager.",
                'policy' => null
            ];
        }
        
        if (strpos($questionLower, 'remote') !== false || strpos($questionLower, 'work from home') !== false) {
            return [
                'answer' => "Remote Work Policy: Employees may request up to 2 days of remote work per week, subject to approval by their department manager and team scheduling constraints.",
                'policy' => null
            ];
        }

        if (strpos($questionLower, 'conduct') !== false || strpos($questionLower, 'behavior') !== false) {
            return [
                'answer' => "Code of Conduct: We expect all employees to maintain a professional, respectful, and inclusive environment. Any form of harassment or discrimination will lead to immediate disciplinary actions.",
                'policy' => null
            ];
        }

        return [
            'answer' => "I couldn't find a specific policy matching your query. Please refer to the HR department or try rephrasing your question (e.g. ask about 'leave', 'remote work', or 'conduct').",
            'policy' => null
        ];
    }
}
