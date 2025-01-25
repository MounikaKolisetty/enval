import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private apiUrl = environment.apiUrl; // Replace with your backend URL 
  constructor(private http: HttpClient) {} 
  signUp(user: any): Observable<any> { 
    return this.http.post(`${this.apiUrl}/signup.php`, user, { responseType: 'json' }); 
  }
  login(email: string, password: string, captchaResponse: string): Observable<any> { 
    return this.http.post(`${this.apiUrl}/login.php`, { email, password, captchaResponse } ,{ responseType: 'json', withCredentials: true });
  }
  requestPasswordReset(email: string): Observable<any> {
     return this.http.post(`${this.apiUrl}/request.php`, { email },{ responseType: 'json' }); 
  } 
  resetPassword(token: string, newPassword: string): Observable<any> {
     return this.http.post(`${this.apiUrl}/reset.php`, { token, newPassword }, { responseType: 'json' }); 
  }
  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout.php`, {} ,{ withCredentials: true }); 
  }
  getUserCourses(userid: string): Observable<any> {
     return this.http.post(`${this.apiUrl}/userCourses.php`, {userid}, { withCredentials: true }); 
  }
  sendHomeContact(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/sendEmail.php`, formData ,{ withCredentials: true });
  }
  sendUserContact(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/contactForm.php`, formData ,{ withCredentials: true });
  }
  sendTrainingForm(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/trainingForm.php`, formData ,{ withCredentials: true });
  }
  sendAdvisorForm(formData: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/advisorForm.php`, formData ,{ withCredentials: true });
  }
  createOrder(amount: number): Observable<any> { 
    return this.http.post<any>(`${this.apiUrl}/create_order.php`, { amount }, { responseType: 'json' }); 
  } 
  verifyPayment(verifyDetails: any): Observable<any> { 
    return this.http.post(`${this.apiUrl}/verify_payment.php`, verifyDetails, { responseType: 'json' }); 
  }
  saveToDb(verificationDetails:any, userDetails: any, paymentVerify: boolean): Observable<any> {
    return this.http.post(`${this.apiUrl}/userDetailsToDB.php`, { verificationDetails, userDetails, paymentVerify }, { responseType: 'json' })
  }
}
