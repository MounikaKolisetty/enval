import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import countryData from '../../assets/json/CountryCodes.json';
import stateData from '../../assets/json/CountryStates.json';
import { PaymentService } from '../services/payment.service';
import { UserService } from '../services/user.service';
import { HttpClientModule, HttpErrorResponse } from '@angular/common/http';
import { ActivatedRoute } from '@angular/router';
declare var Razorpay: any;
@Component({
  selector: 'app-secure-payment',
  standalone: true,
  imports: [ FormsModule,
             CommonModule,
             HttpClientModule,
             ReactiveFormsModule
   ],
  providers: [UserService, PaymentService],
  templateUrl: './secure-payment.component.html',
  styleUrl: './secure-payment.component.css'
})
export class SecurePaymentComponent{
  courseTitle: string = '';
  paymentForm: FormGroup;
  formSubmitted: boolean = false;

  constructor(private paymentService: PaymentService, private userService: UserService, private route: ActivatedRoute, private fb: FormBuilder) {
    this.paymentForm = this.fb.group({ 
      userTitle: ['', Validators.required],
      userName: ['', Validators.required], 
      userContact: ['', [Validators.required, Validators.pattern(/^\d{10}$/)]], 
      userDesignation: ['', Validators.required],
      userDepartment: ['', Validators.required],
      userOrganization: ['', Validators.required],
      userLocation: ['', Validators. required],
      userBusinessArea: ['', Validators. required],
      userVAVME: ['', Validators. required],
      userResponsibilities: ['', Validators. required],
      userDegree: ['', Validators. required],
      userPGDegree: ['', Validators. required],
      userOtherDegree: ['', Validators. required],
      userSponsoredby: ['', Validators. required],
      userPurpose: ['', Validators. required],
      userUsage: ['', Validators. required],
      userExpectation: ['', Validators. required],
      userEmail: ['', [Validators.required, Validators.email]], 
    });
  } 
  
  ngOnInit() { 
    this.route.params.subscribe(params => { 
      this.courseTitle = params['courseTitle']; });
  } 

  createOrder() { 
    if (this.paymentForm.valid) {
    const formValue = this.paymentForm.value;
    const amount = 20000; // Amount in INR 
    this.userService.createOrder(amount * 100).subscribe( 
      (response:any) => { 
        console.log(response)
        const orderId = response.orderId; 
        this.paymentService.setOrderDetails(amount, formValue.userName, formValue.userEmail, formValue.userContact, orderId, this.courseTitle); 
        this.paymentService.openRazorpay(); 
        this.paymentService.processUserDetails(formValue);
      }, 
      (error: HttpErrorResponse) => { 
        console.error('Error creating order:', error); 
      } 
    ); 
  }
  else { // Handle invalid form 
    this.formSubmitted = true;
  }
} 

  
}
