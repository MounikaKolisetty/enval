import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = environment.apiUrl; // Replace with your backend URL 
  csrfToken: string | null = null;
  constructor(private http: HttpClient) {
    this.getCsrfToken();
  } 
  getCsrfToken() {
    this.http.get<{ csrf_token: string }>(`${this.apiUrl}/csrfToken.php`)
      .subscribe(response => {
        this.csrfToken = response.csrf_token;
        console.log('Running...', this.csrfToken);
        localStorage.setItem('csrf_token', this.csrfToken); // Store token
      });
  }
  signUp(user: any): Observable<any> { 
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
    return this.http.post(`${this.apiUrl}/signup.php`, user, { responseType: 'json', headers });
  }
  login(email: string, password: string, captchaResponse: string): Observable<any> { 
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
    return this.http.post(
      `${this.apiUrl}/login.php`, 
      { email, password, captchaResponse } ,
      { responseType: 'json', withCredentials: true , headers}
    );
  }
  requestPasswordReset(email: string): Observable<any> {
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
     return this.http.post(`${this.apiUrl}/request.php`, { email },{ responseType: 'json', headers }); 
  } 
  resetPassword(token: string, newPassword: string): Observable<any> {
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
     return this.http.post(`${this.apiUrl}/reset.php`, { token, newPassword }, { responseType: 'json', headers }); 
  }
  logout(): Observable<any> {
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
    return this.http.post(`${this.apiUrl}/logout.php`, {} ,{ withCredentials: true, headers }); 
  }
  getUserCourses(): Observable<any> {
     return this.http.post(`${this.apiUrl}/userCourses.php`, {}, { withCredentials: true }); 
  }
  sendHomeContact(formData: any, captchaResponse: string): Observable<any>{
    return this.http.post(`${this.apiUrl}/sendEmail.php`, { formData, captchaResponse } ,{ withCredentials: true });
  }
  sendUserContact(formData: any, captchaResponse: string): Observable<any>{
    return this.http.post(`${this.apiUrl}/contactForm.php`, { formData, captchaResponse } ,{ withCredentials: true });
  }
  sendTrainingForm(formData: any, captchaResponse: string): Observable<any>{
    return this.http.post(`${this.apiUrl}/trainingForm.php`, { formData, captchaResponse }  ,{ withCredentials: true });
  }
  sendAdvisorForm(formData: any, captchaResponse: string): Observable<any>{
    return this.http.post(`${this.apiUrl}/advisorForm.php`, { formData, captchaResponse } ,{ withCredentials: true });
  }
  createOrder(amount: number): Observable<any> { 
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
    return this.http.post<any>(
      `${this.apiUrl}/create_order.php`,
       { amount }, 
       { responseType: 'json', headers }
    ); 
  } 
  verifyPayment(verifyDetails: any): Observable<any> { 
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
    return this.http.post(`${this.apiUrl}/verify_payment.php`, verifyDetails, { responseType: 'json', headers }); 
  }
  saveToDb(verificationDetails:any, userDetails: any, paymentVerify: boolean): Observable<any> {
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
    return this.http.post(
      `${this.apiUrl}/userDetailsToDB.php`, 
      { verificationDetails, userDetails, paymentVerify }, 
      { responseType: 'json', headers })
  }
  verifyEmail(token: string): Observable<any> {
    console.log('verifying email', localStorage.getItem('csrf_token'));
    const headers = { 'X-CSRF-Token': localStorage.getItem('csrf_token') || '' };
    return this.http.post(`${this.apiUrl}/verifyEmail.php`, { token }, { responseType: 'json', headers });
  }
}
