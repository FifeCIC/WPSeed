/**
 * WPSeed Verification JavaScript Logger
 * Enhanced logging for verification process debugging
 *
 * @package WPSeed
 * @version 1.0.0
 */

window.WPSeedVerificationLogger = {
    
    context: 'verification',
    stepCounts: {},
    startTime: performance.now(),
    
    /**
     * Initialize verification logging
     */
    init: function() {
        this.startTime = performance.now();
        this.stepCounts = {};
        console.log('WPSeed_Verification: Logging initialized');
    },
    
    /**
     * Log verification step with data counts
     */
    logStep: function(stepName, inputCount, outputCount, details = {}) {
        const elapsed = (performance.now() - this.startTime).toFixed(2);
        const dataLoss = inputCount > outputCount;
        const lossAmount = inputCount - outputCount;
        
        const logData = {
            step: stepName,
            input: inputCount,
            output: outputCount,
            dataLoss: dataLoss,
            lossAmount: lossAmount,
            elapsed: elapsed + 'ms',
            ...details
        };
        
        // Store step count
        this.stepCounts[stepName] = outputCount;
        
        // Log with appropriate level
        if (dataLoss) {
            console.error(`WPSeed_Verification: DATA LOSS in ${stepName}: ${inputCount} → ${outputCount} (lost ${lossAmount})`, logData);
        } else {
            console.log(`WPSeed_Verification: ${stepName}: ${inputCount} → ${outputCount} (+${elapsed}ms)`, logData);
        }
        
        // Use unified logger if available
        if (window.WPSeedLogger) {
            window.WPSeedLogger.trace('VERIFICATION_STEP', `${stepName}: ${inputCount} → ${outputCount}`, logData);
        }
    },
    
    /**
     * Log file processing in loops
     */
    logFileProcessing: function(loopId, fileName, resultCount, details = {}) {
        const loopKey = `file_loop_${loopId}`;
        
        if (!this.stepCounts[loopKey]) {
            this.stepCounts[loopKey] = 0;
            console.log(`WPSeed_Verification: Starting file loop: ${loopId}`);
        }
        
        this.stepCounts[loopKey]++;
        
        // Log every 10th file or if forced
        if (this.stepCounts[loopKey] % 10 === 0 || details.forceLog) {
            console.log(`WPSeed_Verification: File ${this.stepCounts[loopKey]}: ${fileName} → ${resultCount} results`, {
                loopId: loopId,
                fileName: fileName,
                resultCount: resultCount,
                iteration: this.stepCounts[loopKey],
                ...details
            });
        }
        
        // Use unified logger
        if (window.WPSeedLogger) {
            window.WPSeedLogger.loopTrace(loopId, {
                fileName: fileName,
                resultCount: resultCount,
                ...details
            });
        }
    },
    
    /**
     * Log AJAX data preparation
     */
    logAjaxPreparation: function(operation, dataCount, payloadSize = null) {
        const details = {
            operation: operation,
            count: dataCount,
            payloadSize: payloadSize
        };
        
        console.log(`WPSeed_Verification: AJAX_PREP - ${operation}: ${dataCount} items` + 
                   (payloadSize ? ` (${payloadSize} bytes)` : ''), details);
        
        // Use unified logger
        if (window.WPSeedLogger) {
            window.WPSeedLogger.trace('AJAX_PREP', `${operation}: ${dataCount} items`, details);
        }
    },
    
    /**
     * Log data aggregation
     */
    logAggregation: function(aggregationType, inputSources, outputCount, details = {}) {
        const totalInput = Array.isArray(inputSources) ? 
            inputSources.reduce((sum, source) => sum + (source.count || 0), 0) : inputSources;
        
        const aggregationData = {
            type: aggregationType,
            inputSources: inputSources,
            totalInput: totalInput,
            outputCount: outputCount,
            ...details
        };
        
        this.logStep(`AGGREGATE_${aggregationType}`, totalInput, outputCount, aggregationData);
    },
    
    /**
     * Log filter operations
     */
    logFilter: function(filterName, inputCount, outputCount, criteria = '') {
        const filterData = {
            filter: filterName,
            criteria: criteria,
            filtered: inputCount - outputCount
        };
        
        this.logStep(`FILTER_${filterName}`, inputCount, outputCount, filterData);
    },
    
    /**
     * Log hash-based filtering
     */
    logHashFilter: function(totalFiles, changedFiles, skippedFiles) {
        const hashData = {
            totalFiles: totalFiles,
            changedFiles: changedFiles,
            skippedFiles: skippedFiles,
            changeRate: ((changedFiles / totalFiles) * 100).toFixed(1) + '%'
        };
        
        console.log(`WPSeed_Verification: HASH_FILTER - ${totalFiles} files, ${changedFiles} changed, ${skippedFiles} skipped`, hashData);
        
        this.logStep('HASH_FILTER', totalFiles, changedFiles, hashData);
    },
    
    /**
     * Log serialization/transmission
     */
    logTransmission: function(operation, originalCount, transmittedCount, serializedSize = null) {
        const transmissionData = {
            operation: operation,
            originalCount: originalCount,
            transmittedCount: transmittedCount,
            serializedSize: serializedSize
        };
        
        if (serializedSize) {
            console.log(`WPSeed_Verification: TRANSMISSION - ${operation}: ${originalCount} → ${transmittedCount} (${serializedSize} bytes)`, transmissionData);
        } else {
            console.log(`WPSeed_Verification: TRANSMISSION - ${operation}: ${originalCount} → ${transmittedCount}`, transmissionData);
        }
        
        this.logStep(`TRANSMISSION_${operation}`, originalCount, transmittedCount, transmissionData);
    },
    
    /**
     * Get verification summary
     */
    getSummary: function() {
        const summary = {
            totalSteps: Object.keys(this.stepCounts).length,
            finalCounts: this.stepCounts,
            duration: (performance.now() - this.startTime).toFixed(2) + 'ms'
        };
        
        console.log('WPSeed_Verification: SUMMARY', summary);
        return summary;
    },
    
    /**
     * End verification logging
     */
    end: function() {
        const summary = this.getSummary();
        console.log(`WPSeed_Verification: Completed in ${summary.duration} with ${summary.totalSteps} steps`);
        
        // Use unified logger
        if (window.WPSeedLogger) {
            window.WPSeedLogger.endContext(summary);
        }
    }
};

// Auto-initialize if in verification context
if (typeof wpVerificationContext !== 'undefined' && wpVerificationContext) {
    WPSeedVerificationLogger.init();
}