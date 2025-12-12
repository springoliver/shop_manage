<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create stoma_emp_payroll_ire_tax_exemption table
        if (!Schema::hasTable('stoma_emp_payroll_ire_tax_exemption')) {
            Schema::create('stoma_emp_payroll_ire_tax_exemption', function (Blueprint $table) {
                $table->bigIncrements('tax_exemption_id');
                $table->string('name', 255);
                $table->string('value', 555)->nullable();
            });
        }

        // Create stoma_emp_payroll_ire_prsi_category table
        if (!Schema::hasTable('stoma_emp_payroll_ire_prsi_category')) {
            Schema::create('stoma_emp_payroll_ire_prsi_category', function (Blueprint $table) {
                $table->increments('prsi_category_id');
                $table->string('name', 255);
                $table->string('value', 255)->nullable();
            });
        }

        // Create stoma_emp_payroll_ire_prsi_class table
        if (!Schema::hasTable('stoma_emp_payroll_ire_prsi_class')) {
            Schema::create('stoma_emp_payroll_ire_prsi_class', function (Blueprint $table) {
                $table->increments('prsi_class_id');
                $table->string('name', 255);
                $table->string('value', 255)->nullable();
            });
        }

        // Create stoma_emp_payroll_ire_usc_standard_cuttoff_points table
        if (!Schema::hasTable('stoma_emp_payroll_ire_usc_standard_cuttoff_points')) {
            Schema::create('stoma_emp_payroll_ire_usc_standard_cuttoff_points', function (Blueprint $table) {
                $table->increments('cuttoff_points_id');
                $table->string('name', 255);
                $table->string('value', 255)->nullable();
            });
        }

        // Create stoma_emp_payroll_ire_prd_calculation_methods table
        if (!Schema::hasTable('stoma_emp_payroll_ire_prd_calculation_methods')) {
            Schema::create('stoma_emp_payroll_ire_prd_calculation_methods', function (Blueprint $table) {
                $table->increments('calculation_methods_id');
                $table->string('name', 255);
                $table->string('value', 255)->nullable();
            });
        }

        // Create stoma_emp_payroll_ire_pension_types table
        if (!Schema::hasTable('stoma_emp_payroll_ire_pension_types')) {
            Schema::create('stoma_emp_payroll_ire_pension_types', function (Blueprint $table) {
                $table->increments('pension_types_id');
                $table->string('name', 255);
                $table->string('value', 255)->nullable();
            });
        }

        // Create stoma_emp_payroll_ire_employee_settings table
        if (!Schema::hasTable('stoma_emp_payroll_ire_employee_settings')) {
            Schema::create('stoma_emp_payroll_ire_employee_settings', function (Blueprint $table) {
                $table->increments('employee_settings_id');
                $table->unsignedInteger('storeid');
                $table->unsignedInteger('employeeid');
                $table->date('prev_employment_leavedate')->nullable();
                $table->integer('prev_employer_no')->nullable();
                $table->string('gross_pay_for_paye', 55)->default('');
                $table->integer('total_pay_for_paye')->default(0);
                $table->integer('gross_pay_for_usc')->default(0);
                $table->integer('total_pay_for_usc')->default(0);
                $table->integer('gross_pay_for_prd')->default(0);
                $table->integer('total_pay_for_prd')->default(0);
                $table->integer('total_pay_for_lpt')->default(0);
                $table->enum('tax_basis', ['emergency_basis', 'week_one_basis', 'cumulitive_basis'])->default('emergency_basis');
                $table->integer('tax_exemption_id')->default(0);
                $table->integer('weekly_tax_credit')->default(0);
                $table->integer('annualy_tax_credit')->default(0);
                $table->integer('weekly_cut_off')->default(0);
                $table->integer('annualy_cut_off')->default(0);
                $table->integer('weekly_cutoff_point0_5')->default(0);
                $table->integer('anualy_cutoff_point0_5')->default(0);
                $table->integer('weekly_cutoff_point2_5')->default(0);
                $table->integer('anualy_cutoff_point2_5')->default(0);
                $table->integer('weekly_cutoff_point5')->default(0);
                $table->integer('anualy_cutoff_point5')->default(0);
                $table->integer('weekly_cutoff_point8')->default(0);
                $table->integer('anualy_cutoff_point8')->default(0);
                $table->integer('prsi_category_id')->default(0);
                $table->integer('calculation_methods_id')->default(0);
                $table->integer('lpd_tobe_reduced')->default(0);
                $table->integer('national_pay_todate')->default(0);
                $table->integer('total_employee_prsi_able_pay_todate')->default(0);
                $table->integer('medical_insurance_pay_todate')->default(0);
                $table->integer('total_employee_prsi_pay_todate')->default(0);
                $table->integer('total_employer_prsi_able_pay_todate')->default(0);
                $table->integer('taxable_ilness_benefit_todate')->default(0);
                $table->integer('total_employer_prsi_pay_todate')->default(0);
                $table->integer('paye_able_pay_todate')->default(0);
                $table->integer('pension_able_pay_todate')->default(0);
                $table->integer('pay_todate')->default(0);
                $table->integer('pension_types_id')->default(0);
                $table->integer('usc_able_pay_todate')->default(0);
                $table->integer('employee_pension_todate')->default(0);
                $table->integer('employer_pension_todate')->default(0);
                $table->integer('prd_able_todate')->default(0);
                $table->integer('prd_todate')->default(0);
                $table->integer('lpd_todate')->default(0);
                $table->integer('prsi_class_id')->default(0);
                $table->integer('employee_previous_prsi_class')->default(0);
                $table->timestamp('insertdatetime')->useCurrent();
                $table->string('insertip', 51)->default('');
                $table->dateTime('editdatetime')->nullable();
                $table->string('editip', 51)->default('');

                // Note: Foreign keys are not added as CI doesn't define them and type mismatches may occur
                // (CI uses int for storeid/employeeid here but bigint in parent tables)
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_emp_payroll_ire_employee_settings');
        Schema::dropIfExists('stoma_emp_payroll_ire_pension_types');
        Schema::dropIfExists('stoma_emp_payroll_ire_prd_calculation_methods');
        Schema::dropIfExists('stoma_emp_payroll_ire_usc_standard_cuttoff_points');
        Schema::dropIfExists('stoma_emp_payroll_ire_prsi_class');
        Schema::dropIfExists('stoma_emp_payroll_ire_prsi_category');
        Schema::dropIfExists('stoma_emp_payroll_ire_tax_exemption');
    }
};

